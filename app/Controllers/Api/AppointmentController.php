<?php

namespace App\Controllers\Api;

use App\Models\AppointmentModel;
use App\Models\DoctorModel;

class AppointmentController extends BaseApiController
{
    public function index()
    {
        $hasUsersTable = $this->hasUsersTable();
        $model = (new AppointmentModel())
            ->select($this->appointmentSelect($hasUsersTable))
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->orderBy('appointments.id', 'DESC');

        if ($hasUsersTable) {
            $model->join('users', 'users.id = appointments.user_id', 'left');
        }

        $userId = $this->currentUserId();
        if ($userId !== null) {
            $model->where('appointments.user_id', $userId);
        }

        $appointments = array_map(fn (array $appointment): array => $this->formatAppointment($appointment), $model->findAll());

        return $this->success($appointments, 'Data booking berhasil diambil.');
    }

    public function show(int $id)
    {
        $appointment = $this->findOwnedAppointment($id);

        if ($appointment === null) {
            return $this->failure('Appointment tidak ditemukan.', [], 404);
        }

        return $this->success($this->formatAppointment($appointment), 'Data booking berhasil diambil.');
    }

    public function create()
    {
        $input = $this->input();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

        $input = $this->normalizeInput($input);

        $rules = [
            'doctor_id' => 'required|integer',
            'date'      => 'required|valid_date[Y-m-d]',
            'time'      => 'required',
            'keluhan'   => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validateData($input, $rules)) {
            return $this->failure('Validasi gagal.', $this->validator->getErrors(), 422);
        }

        if ((new DoctorModel())->find((int) $input['doctor_id']) === null) {
            return $this->failure('Dokter tidak ditemukan.', [], 404);
        }

        $userId = $this->currentUserId() ?? (isset($input['user_id']) ? (int) $input['user_id'] : null);
        if ($userId === null || $userId <= 0) {
            return $this->failure('Token autentikasi atau user_id diperlukan.', [], 401);
        }

        $model = new AppointmentModel();
        $model->insert([
            'user_id'   => $userId,
            'doctor_id' => (int) $input['doctor_id'],
            'date'      => $input['date'],
            'time'      => $input['time'],
            'keluhan'   => $input['keluhan'] ?? null,
            'status'    => 'waiting',
            'no_antrian' => null,
        ]);

        $appointment = $this->findAppointment((int) $model->getInsertID());
        $formatted = $this->formatAppointment($appointment);
        $formatted['payment_method'] = $input['payment_method'] ?? $input['metode_pembayaran'] ?? null;
        $formatted['metode_pembayaran'] = $formatted['payment_method'];
        $formatted['total'] = (int) ($input['total'] ?? $formatted['total'] ?? 0);

        return $this->success($formatted, 'Booking berhasil dibuat.', 201);
    }

    public function update(int $id)
    {
        $appointment = $this->findOwnedAppointment($id);

        if ($appointment === null) {
            return $this->failure('Booking tidak ditemukan.', [], 404);
        }

        $input = $this->normalizeInput($this->input());
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

        $data = array_filter([
            'doctor_id' => isset($input['doctor_id']) ? (int) $input['doctor_id'] : null,
            'date'      => $input['date'] ?? null,
            'time'      => $input['time'] ?? null,
            'keluhan'   => $input['keluhan'] ?? null,
            'status'    => $input['status'] ?? null,
        ], static fn ($value): bool => $value !== null);

        if ($data === []) {
            return $this->failure('Tidak ada data booking yang diubah.', [], 422);
        }

        (new AppointmentModel())->update($id, $data);

        return $this->success($this->formatAppointment($this->findAppointment($id)), 'Booking berhasil diupdate.');
    }

    public function cancel(int $id)
    {
        $appointment = $this->findOwnedAppointment($id);

        if ($appointment === null) {
            return $this->failure('Booking tidak ditemukan.', [], 404);
        }

        if (in_array($appointment['status'], ['paid', 'done', 'cancelled'], true)) {
            return $this->failure('Appointment tidak dapat dibatalkan.', [], 422);
        }

        (new AppointmentModel())->update($id, ['status' => 'cancelled']);

        return $this->success($this->formatAppointment($this->findAppointment($id)), 'Booking berhasil dibatalkan.');
    }

    public function queue(int $id)
    {
        $appointment = $this->findOwnedAppointment($id);

        if ($appointment === null) {
            return $this->failure('Appointment tidak ditemukan.', [], 404);
        }

        return $this->success([
            'appointment_id' => (int) $appointment['id'],
            'queue_number'   => $appointment['no_antrian'],
            'status'         => $appointment['status'],
        ]);
    }

    private function findOwnedAppointment(int $id): ?array
    {
        $hasUsersTable = $this->hasUsersTable();
        $model = (new AppointmentModel())
            ->select($this->appointmentSelect($hasUsersTable))
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->where('appointments.id', $id);

        if ($hasUsersTable) {
            $model->join('users', 'users.id = appointments.user_id', 'left');
        }

        $userId = $this->currentUserId();
        if ($userId !== null) {
            $model->where('appointments.user_id', $userId);
        }

        return $model->first();
    }

    private function findAppointment(int $id): ?array
    {
        $hasUsersTable = $this->hasUsersTable();
        $model = (new AppointmentModel())
            ->select($this->appointmentSelect($hasUsersTable))
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->where('appointments.id', $id);

        if ($hasUsersTable) {
            $model->join('users', 'users.id = appointments.user_id', 'left');
        }

        return $model->first();
    }

    private function appointmentSelect(bool $hasUsersTable): string
    {
        $select = 'appointments.*, doctors.name AS doctor_name, doctors.specialization, doctors.schedule';

        if ($hasUsersTable) {
            $select .= ', users.username AS patient_name';
        }

        return $select;
    }

    private function hasUsersTable(): bool
    {
        return db_connect()->tableExists('users');
    }

    private function normalizeInput(array $input): array
    {
        $input['doctor_id'] = $input['doctor_id'] ?? $input['id_dokter'] ?? null;
        $input['user_id'] = $input['user_id'] ?? $input['id_user'] ?? null;
        $input['date'] = $input['date'] ?? $input['tanggal'] ?? null;
        $input['time'] = $input['time'] ?? $input['jam'] ?? null;
        $input['keluhan'] = $input['keluhan'] ?? $input['complaint'] ?? null;
        $input['payment_method'] = $input['payment_method'] ?? $input['metode_pembayaran'] ?? null;

        return $input;
    }

    private function formatAppointment(?array $appointment): array
    {
        if ($appointment === null) {
            return [];
        }

        $id = (int) ($appointment['id'] ?? 0);
        $queue = $appointment['no_antrian'] ?? null;

        return array_merge($appointment, [
            'booking_id'       => $id,
            'id_booking'       => $id,
            'code'             => 'KM-' . $id,
            'booking_code'     => 'KM-' . $id,
            'kode_booking'     => 'KM-' . $id,
            'queue_number'     => $queue,
            'nomor_antrian'    => $queue,
            'doctor_id'        => (int) ($appointment['doctor_id'] ?? 0),
            'id_dokter'        => (int) ($appointment['doctor_id'] ?? 0),
            'doctor_name'      => $appointment['doctor_name'] ?? 'Dokter',
            'nama_dokter'      => $appointment['doctor_name'] ?? 'Dokter',
            'patient_name'     => $appointment['patient_name'] ?? '-',
            'nama_pasien'      => $appointment['patient_name'] ?? '-',
            'tanggal'          => $appointment['date'] ?? null,
            'jam'              => $appointment['time'] ?? null,
            'complaint'        => $appointment['keluhan'] ?? '',
            'payment_method'   => $appointment['payment_method'] ?? null,
            'metode_pembayaran'=> $appointment['payment_method'] ?? null,
            'total'            => (int) ($appointment['amount'] ?? 0),
            'doctor'           => [
                'id'             => (int) ($appointment['doctor_id'] ?? 0),
                'name'           => $appointment['doctor_name'] ?? 'Dokter',
                'nama_dokter'    => $appointment['doctor_name'] ?? 'Dokter',
                'specialization' => $appointment['specialization'] ?? '',
                'specialty'      => $appointment['specialization'] ?? '',
                'practice_time'  => $appointment['schedule'] ?? '',
                'jadwal_praktik' => $appointment['schedule'] ?? '',
            ],
        ]);
    }
}

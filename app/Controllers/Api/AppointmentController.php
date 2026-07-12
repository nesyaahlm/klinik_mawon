<?php

namespace App\Controllers\Api;

use App\Models\AppointmentModel;
use App\Models\DoctorScheduleModel;
use App\Models\DoctorModel;
use App\Models\PaymentModel;
use Throwable;

class AppointmentController extends BaseApiController
{
    public function index()
    {
        $userId = $this->currentUserId();
        if ($userId === null) {
            return $this->failure('Token autentikasi diperlukan.', [], 401);
        }

        $hasUsersTable = $this->hasUsersTable();
        $hasPaymentsTable = $this->hasPaymentsTable();
        $model = (new AppointmentModel())
            ->select($this->appointmentSelect($hasUsersTable, $hasPaymentsTable))
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->orderBy('appointments.id', 'DESC');

        if ($hasUsersTable) {
            $model->join('users', 'users.id = appointments.user_id', 'left');
        }
        if ($hasPaymentsTable) {
            $model->join('payments', 'payments.appointment_id = appointments.id', 'left');
        }

        $model->where('appointments.user_id', $userId);

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
            'doctor_id'      => 'required|integer',
            'schedule_id'    => 'permit_empty|integer',
            'date'           => 'permit_empty|valid_date[Y-m-d]',
            'time'           => 'permit_empty',
            'payment_method' => 'required|in_list[cash,bank_transfer,qris]',
            'keluhan'        => 'required|max_length[1000]',
        ];

        if (! $this->validateData($input, $rules)) {
            return $this->failure('Validasi gagal.', $this->validator->getErrors(), 422);
        }

        $doctor = (new DoctorModel())->find((int) $input['doctor_id']);
        if ($doctor === null) {
            return $this->failure('Dokter tidak ditemukan.', [], 404);
        }

        $userId = $this->currentUserId();
        if ($userId === null) {
            return $this->failure('Token autentikasi diperlukan.', [], 401);
        }

        $schedule = $this->resolveSchedule($input, (int) $input['doctor_id']);
        if ($schedule === false) {
            return $this->failure('Jadwal tidak ditemukan atau bukan milik dokter yang dipilih.', [], 404);
        }

        if (($input['date'] ?? '') === '' && is_array($schedule)) {
            $input['date'] = $this->scheduleDate($schedule);
        }
        if (($input['time'] ?? '') === '' && is_array($schedule)) {
            $input['time'] = $this->scheduleTime($schedule);
        }
        if (($input['date'] ?? '') === '' || ($input['time'] ?? '') === '') {
            return $this->failure('Tanggal dan jam booking wajib dipilih.', [], 422);
        }

        $existingBooking = (new AppointmentModel())
            ->where('user_id', $userId)
            ->whereNotIn('status', ['cancelled', 'canceled', 'done', 'completed'])
            ->first();

        if ($existingBooking !== null) {
            return $this->failure(
                'Anda sudah memiliki booking aktif. Hanya satu booking aktif yang diperbolehkan.',
                [],
                409,
            );
        }

        $db = db_connect();
        $appointmentModel = new AppointmentModel();
        $paymentTable = $this->paymentTable();
        $amount = (int) ($input['total'] ?? $doctor['fee'] ?? 50000);
        if ($amount <= 0) {
            $amount = 50000;
        }
        $appointmentStatus = $input['payment_method'] === 'cash' ? 'confirmed' : 'waiting_payment';
        $paymentStatus = $input['payment_method'] === 'cash' ? 'pay_at_clinic' : 'unpaid';

        $db->transStart();
        try {
            $appointmentData = $this->filterExistingFields('appointments', [
            'user_id'        => $userId,
            'doctor_id'      => (int) $input['doctor_id'],
            'schedule_id'    => $input['schedule_id'] ?? null,
            'date'           => $input['date'],
            'time'           => $input['time'],
            'keluhan'        => $input['keluhan'],
            'status'         => $appointmentStatus,
            'payment_method' => $input['payment_method'],
            'amount'         => $amount,
            'no_antrian'     => null,
            ]);

            $appointmentModel->insert($appointmentData);
            $appointmentId = (int) $appointmentModel->getInsertID();

            $paymentData = $this->filterExistingFields($paymentTable, [
                'appointment_id' => $appointmentId,
                'payment_method' => $input['payment_method'],
                'amount'         => $amount,
                'status'         => $paymentStatus,
                'payment_date'   => date('Y-m-d H:i:s'),
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
            if ($paymentData !== [] && $this->safeTableExists($paymentTable)) {
                db_connect()->table($paymentTable)->insert($paymentData);
            }

            $db->transComplete();
        } catch (Throwable $e) {
            $db->transRollback();
            return $this->failure('Booking gagal dibuat.', ['server' => $e->getMessage()], 500);
        }

        if (! $db->transStatus()) {
            return $this->failure('Booking gagal dibuat.', [], 500);
        }

        $appointment = $this->findAppointment($appointmentId);
        $formatted = $this->formatAppointment($appointment);

        return $this->success([
            'booking' => $formatted,
            'payment' => [
                'appointment_id'  => $appointmentId,
                'payment_method'  => $input['payment_method'],
                'amount'          => $amount,
                'status'          => $paymentStatus,
                'bank'            => $input['payment_method'] === 'bank_transfer' ? $this->bankConfig() : null,
                'qris_url'        => $input['payment_method'] === 'qris' ? base_url('img/qr.png') : null,
            ],
        ], 'Booking berhasil dibuat.', 201);
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
        $hasPaymentsTable = $this->hasPaymentsTable();
        $model = (new AppointmentModel())
            ->select($this->appointmentSelect($hasUsersTable, $hasPaymentsTable))
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->where('appointments.id', $id);

        if ($hasUsersTable) {
            $model->join('users', 'users.id = appointments.user_id', 'left');
        }
        if ($hasPaymentsTable) {
            $model->join('payments', 'payments.appointment_id = appointments.id', 'left');
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
        $hasPaymentsTable = $this->hasPaymentsTable();
        $model = (new AppointmentModel())
            ->select($this->appointmentSelect($hasUsersTable, $hasPaymentsTable))
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->where('appointments.id', $id);

        if ($hasUsersTable) {
            $model->join('users', 'users.id = appointments.user_id', 'left');
        }
        if ($hasPaymentsTable) {
            $model->join('payments', 'payments.appointment_id = appointments.id', 'left');
        }

        return $model->first();
    }

    private function appointmentSelect(bool $hasUsersTable, bool $hasPaymentsTable): string
    {
        $select = 'appointments.*, doctors.name AS doctor_name, doctors.specialization, doctors.photo AS doctor_photo, doctors.schedule';

        if ($hasUsersTable) {
            $select .= ', users.username AS patient_name';
        }
        if ($hasPaymentsTable) {
            $select .= ', payments.status AS payment_status';
            if ($this->safeFieldExists('proof', 'payments')) {
                $select .= ', payments.proof AS payment_proof';
            }
            if ($this->safeFieldExists('bukti', 'payments')) {
                $select .= ', payments.bukti AS payment_bukti';
            }
        }

        return $select;
    }

    private function hasUsersTable(): bool
    {
        return $this->safeTableExists('users');
    }

    private function hasPaymentsTable(): bool
    {
        return $this->safeTableExists('payments');
    }

    private function normalizeInput(array $input): array
    {
        $input['doctor_id'] = $input['doctor_id'] ?? $input['id_dokter'] ?? null;
        $input['schedule_id'] = $input['schedule_id'] ?? $input['id_schedule'] ?? $input['jadwal_id'] ?? null;
        $input['date'] = $input['date'] ?? $input['tanggal'] ?? null;
        $input['time'] = $input['time'] ?? $input['jam'] ?? null;
        $input['keluhan'] = $input['keluhan'] ?? $input['complaint'] ?? null;
        $input['payment_method'] = $input['payment_method'] ?? $input['metode_pembayaran'] ?? null;
        if ($input['payment_method'] === 'transfer') {
            $input['payment_method'] = 'bank_transfer';
        }

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
            'payment_status'   => $appointment['payment_status'] ?? null,
            'total'            => (int) ($appointment['amount'] ?? 0),
            'doctor'           => [
                'id'             => (int) ($appointment['doctor_id'] ?? 0),
                'name'           => $appointment['doctor_name'] ?? 'Dokter',
                'nama_dokter'    => $appointment['doctor_name'] ?? 'Dokter',
                'specialization' => $appointment['specialization'] ?? '',
                'specialty'      => $appointment['specialization'] ?? '',
                'poli'           => $appointment['specialization'] ?? '',
                'polyclinic'     => $appointment['specialization'] ?? '',
                'practice_time'  => $appointment['schedule'] ?? '',
                'jadwal_praktik' => $appointment['schedule'] ?? '',
                'photo'          => $this->normalizePhotoUrl($appointment['doctor_photo'] ?? ''),
                'photo_url'      => $this->normalizePhotoUrl($appointment['doctor_photo'] ?? ''),
            ],
        ]);
    }

    private function resolveSchedule(array &$input, int $doctorId): array|bool|null
    {
        if (! $this->safeTableExists('doctor_schedule')) {
            return null;
        }

        $scheduleId = $input['schedule_id'] ?? null;
        if ($scheduleId !== null && $scheduleId !== '') {
            $schedule = (new DoctorScheduleModel())->find((int) $scheduleId);
            if ($schedule === null || (int) ($schedule['doctor_id'] ?? 0) !== $doctorId) {
                return false;
            }
            $input['schedule_id'] = (int) $scheduleId;
            return $schedule;
        }

        return null;
    }

    private function scheduleDate(array $schedule): string
    {
        if (! empty($schedule['date'])) {
            return (string) $schedule['date'];
        }

        return date('Y-m-d');
    }

    private function scheduleTime(array $schedule): string
    {
        return substr((string) ($schedule['start_time'] ?? $schedule['time'] ?? '08:00'), 0, 5);
    }

    private function bankConfig(): array
    {
        return [
            'bank_name' => 'BCA',
            'account_number' => '1234567890',
            'account_name' => 'Klinik Mawon',
        ];
    }

    private function paymentTable(): string
    {
        return $this->safeTableExists('payments') ? 'payments' : 'mobile_payments';
    }
}

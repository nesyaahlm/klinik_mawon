<?php

namespace App\Controllers\Api;

use App\Models\AppointmentModel;
use App\Models\DoctorModel;

class AppointmentController extends BaseApiController
{
    public function index()
    {
        $appointments = (new AppointmentModel())
            ->select('appointments.*, doctors.name AS doctor_name, doctors.specialization')
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->where('appointments.user_id', $this->currentUserId())
            ->orderBy('appointments.id', 'DESC')
            ->findAll();

        return $this->success($appointments);
    }

    public function show(int $id)
    {
        $appointment = $this->findOwnedAppointment($id);

        if ($appointment === null) {
            return $this->failure('Appointment tidak ditemukan.', [], 404);
        }

        return $this->success($appointment);
    }

    public function create()
    {
        $input = $this->input();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

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

        $model = new AppointmentModel();
        $model->insert([
            'user_id'   => $this->currentUserId(),
            'doctor_id' => (int) $input['doctor_id'],
            'date'      => $input['date'],
            'time'      => $input['time'],
            'keluhan'   => $input['keluhan'] ?? null,
            'status'    => 'waiting',
            'no_antrian' => null,
        ]);

        return $this->success($this->findOwnedAppointment((int) $model->getInsertID()), 'Appointment berhasil dibuat.', 201);
    }

    public function cancel(int $id)
    {
        $appointment = $this->findOwnedAppointment($id);

        if ($appointment === null) {
            return $this->failure('Appointment tidak ditemukan.', [], 404);
        }

        if (in_array($appointment['status'], ['paid', 'done', 'cancelled'], true)) {
            return $this->failure('Appointment tidak dapat dibatalkan.', [], 422);
        }

        (new AppointmentModel())->update($id, ['status' => 'cancelled']);

        return $this->success($this->findOwnedAppointment($id), 'Appointment berhasil dibatalkan.');
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
        return (new AppointmentModel())
            ->select('appointments.*, doctors.name AS doctor_name, doctors.specialization')
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->where('appointments.id', $id)
            ->where('appointments.user_id', $this->currentUserId())
            ->first();
    }
}

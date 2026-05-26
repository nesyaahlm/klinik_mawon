<?php

namespace App\Controllers\Api;

use App\Controllers\RestfullController;
use App\Models\AppointmentModel;
use App\Models\QueueModel;

class Appointment extends RestfullController
{
    protected $appointmentModel;
    protected $queueModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->queueModel       = new QueueModel();
    }

    // GET ALL APPOINTMENT
    public function index()
    {
        $appointments = $this->appointmentModel
            ->select('appointments.*, 
                      users.username, 
                      doctors.name AS doctor_name,
                      payments.proof')
            ->join('users', 'users.id = appointments.user_id', 'left')
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->join('payments', 'payments.appointment_id = appointments.id', 'left')
            ->orderBy('appointments.id', 'ASC')
            ->findAll();

        return $this->responseHasil(200, true, $appointments);
    }

    // GET DETAIL APPOINTMENT
    public function show($id = null)
    {
        $appointment = $this->appointmentModel
            ->select('appointments.*, 
                      users.username, 
                      doctors.name AS doctor_name,
                      payments.proof')
            ->join('users', 'users.id = appointments.user_id', 'left')
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->join('payments', 'payments.appointment_id = appointments.id', 'left')
            ->where('appointments.id', $id)
            ->first();

        if (!$appointment) {
            return $this->responseHasil(404, false, 'Appointment tidak ditemukan');
        }

        return $this->responseHasil(200, true, $appointment);
    }

    // CREATE APPOINTMENT
    public function create()
    {
        $input = $this->request->getJSON(true);

        $data = [
            'user_id'   => $input['user_id'] ?? null,
            'doctor_id' => $input['doctor_id'] ?? null,
            'date'      => $input['date'] ?? null,
            'time'      => $input['time'] ?? null,
            'status'    => $input['status'] ?? 'pending',
        ];

        $this->appointmentModel->insert($data);

        return $this->responseHasil(201, true, 'Appointment berhasil ditambahkan');
    }

    // UPDATE APPOINTMENT
    public function update($id = null)
    {
        $appointment = $this->appointmentModel->find($id);

        if (!$appointment) {
            return $this->responseHasil(404, false, 'Appointment tidak ditemukan');
        }

        $input = $this->request->getJSON(true);

        $data = [
            'user_id'   => $input['user_id'] ?? $appointment['user_id'],
            'doctor_id' => $input['doctor_id'] ?? $appointment['doctor_id'],
            'date'      => $input['date'] ?? $appointment['date'],
            'time'      => $input['time'] ?? $appointment['time'],
            'status'    => $input['status'] ?? $appointment['status'],
        ];

        $this->appointmentModel->update($id, $data);

        $newStatus = $data['status'];

        // AUTO NOMOR ANTRIAN
        if (($newStatus === 'confirmed' || $newStatus === 'done') 
            && $appointment['no_antrian'] == null) {

            $last = $this->appointmentModel
                ->where('date', $appointment['date'])
                ->selectMax('no_antrian')
                ->first();

            $queueNumber = ($last['no_antrian'] ?? 0) + 1;

            $this->appointmentModel->update($id, [
                'no_antrian' => $queueNumber
            ]);

            $this->queueModel->insert([
                'patient_name' => $appointment['patient_name'] ?? 'Pasien',
                'service'      => 'Konsultasi',
                'queue_number' => $queueNumber
            ]);
        }

        return $this->responseHasil(200, true, 'Appointment berhasil diupdate');
    }

    // DELETE APPOINTMENT
    public function delete($id = null)
    {
        $appointment = $this->appointmentModel->find($id);

        if (!$appointment) {
            return $this->responseHasil(404, false, 'Appointment tidak ditemukan');
        }

        $this->appointmentModel->delete($id);

        return $this->responseHasil(200, true, 'Appointment berhasil dihapus');
    }
}
<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\DoctorModel;
use App\Models\UserModel;
use App\Models\QueueModel;

class Confirmation extends BaseController
{
    public function manual($appointmentId)
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $appointmentModel = new AppointmentModel();
        $queueModel       = new QueueModel();

        $appointment = $appointmentModel
        ->select('appointments.*, profiles.name AS patient_name')
        ->join('profiles', 'profiles.user_id = appointments.user_id')

            ->find($appointmentId);

        if (!$appointment || $appointment['user_id'] !== user_id()) {
            return redirect()->to('/history')->with('error', 'Akses tidak valid atau appointment tidak ditemukan.');
        }

        if ($appointment['no_antrian'] === null && $appointment['status'] === 'done') {

            $last = $appointmentModel
                ->where('date', $appointment['date'])
                ->selectMax('no_antrian')
                ->first();

            $queueNumber = ($last['no_antrian'] ?? 0) + 1;

            $appointmentModel->update($appointmentId, [
                'no_antrian' => $queueNumber
            ]);

            $queueModel->insert([
                'patient_name' => $appointment['patient_name'],
                'service'      => 'Konsultasi',
                'queue_number' => $queueNumber
            ]);
        }

        return redirect()->to('/confirmation/' . $appointmentId);
    }

    public function index($appointmentId)
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $appointmentModel = new AppointmentModel();
        $doctorModel      = new DoctorModel();
        $userModel        = new UserModel();
        $queueModel       = new QueueModel();

        $appointment = $appointmentModel
        ->select('appointments.*, profiles.name AS patient_name')
        ->join('profiles', 'profiles.user_id = appointments.user_id')

            ->find($appointmentId);

        if (!$appointment) {
            return redirect()->to('/')
                ->with('error', 'Data appointment tidak ditemukan');
        }

        if ($appointment['user_id'] !== user_id()) {
            return redirect()->to('/history')
                ->with('error', 'Akses tidak valid.');
        }

        if (($appointment['status'] === 'done' || $appointment['status'] === 'confirmed')
    && $appointment['no_antrian'] == null) {


            $last = $appointmentModel
                ->where('date', $appointment['date'])
                ->selectMax('no_antrian')
                ->first();

            $queueNumber = ($last['no_antrian'] ?? 0) + 1;

            $appointmentModel->update($appointmentId, [
                'no_antrian' => $queueNumber
            ]);

            $queueModel->insert([
                'patient_name' => $appointment['patient_name'],
                'service'      => 'Konsultasi',
                'queue_number' => $queueNumber
            ]);

            return redirect()->to('/confirmation/' . $appointmentId);
        }

        if ($appointment['status'] === 'pending') {
            $user_status = 'Menunggu';
        } elseif ($appointment['status'] === 'confirmed') {
            $user_status = 'COD';
        } elseif ($appointment['status'] === 'done') {
            $user_status = null;
        } else {
            $user_status = 'Unknown';
        }

        if ($user_status) {
            return view('waiting_confirmation', [
                'appointment' => $appointment,
                'user_status' => $user_status
            ]);
        }

        return view('confirmation', [
            'appointment' => $appointment,
            'doctor'      => $doctorModel->find($appointment['doctor_id']),
            'user'        => $userModel->find($appointment['user_id'])
        ]);
    }
}

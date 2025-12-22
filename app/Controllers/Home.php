<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use Myth\Auth\Models\UserModel;
use App\Models\DoctorModel;

class Home extends BaseController
{
    public function index()
    {
        if (logged_in()) {
            if (in_groups('admin')) {
                return redirect()->to('/admin/dashboard');
            }
        }

        return view('home');
    }

    public function services()
    {
        return view('services');
    }



    public function schedule($doctorId)
    {
        $doctorModel = new \App\Models\DoctorModel();

        $doctor = $doctorModel->find($doctorId);

        if (!$doctor) {
            return redirect()->to('/doctors')
                ->with('error', 'Dokter tidak ditemukan');
        }

        $scheduleTimes = [
            '08:00',
            '09:00',
            '10:00',
            '11:00',
            '13:00',
            '14:00',
            '15:00'
        ];

        return view('schedule', [
            'doctor' => $doctor,
            'id' => $doctorId,
            'scheduleTimes' => $scheduleTimes
        ]);
    }

    public function scheduleProcess()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $appointmentModel = new \App\Models\AppointmentModel();
        $userId = user_id();

        $data = [
            'user_id' => $userId,
            'doctor_id' => $this->request->getPost('doctor_id'),
            'date' => $this->request->getPost('date'),
            'time' => $this->request->getPost('time'),
            'status' => 'waiting',
            'no_antrian' => null
        ];

        $appointmentModel->insert($data);
        $appointmentId = $appointmentModel->insertID();

        session()->set('appointment_id', $appointmentId);

        return redirect()->to('/payment');
    }
    public function confirmation($appointmentId)
    {
        $appointmentModel = new AppointmentModel();
        $userModel = new UserModel();
        $doctorModel = new DoctorModel();

        $appointment = $appointmentModel->find($appointmentId);
        if (!$appointment) {
            return redirect()->to('/doctors')->with('error', 'Appointment tidak ditemukan.');
        }


        if ($appointment['status'] !== 'paid') {
            session()->set('appointment_id', $appointmentId);
            return redirect()->to('/payment')
                ->with('error', 'Silakan lakukan pembayaran terlebih dahulu.');
        }

        $user = $userModel->find($appointment['user_id']);
        $doctor = $doctorModel->find($appointment['doctor_id']);

        return view('confirmation', [
            'appointment' => $appointment,
            'user' => $user,
            'doctor' => $doctor,
            'no_antrian' => $appointment['no_antrian']
        ]);
    }


    public function history()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $appointmentModel = new AppointmentModel();
        $userId = user_id();

        $appointments = $appointmentModel
            ->select('appointments.id,
                  appointments.date,
                  appointments.time,
                  appointments.status,
                  appointments.no_antrian,
                  doctors.name AS doctor_name,
                  users.username AS patient_name')
            ->join('doctors', 'doctors.id = appointments.doctor_id')
            ->join('users', 'users.id = appointments.user_id')
            ->where('appointments.user_id', $userId)
            ->orderBy('appointments.id', 'DESC')
            ->findAll();

        return view('history', ['appointments' => $appointments]);
    }

}
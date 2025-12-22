<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\AppointmentModel;
use App\Models\QueueModel;
use App\Models\UserModel;

class Appointment extends BaseController
{
    public function index()
    {
        $appointmentModel = new AppointmentModel();

        $data['appointments'] = $appointmentModel
            ->select('appointments.*, 
                      users.username, 
                      doctors.name AS doctor_name,
                      payments.proof')
            ->join('users', 'users.id = appointments.user_id', 'left')
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->join('payments', 'payments.appointment_id = appointments.id', 'left')
            ->orderBy('appointments.id', 'ASC')
            ->findAll();

        return view('admin/appointment/index', $data);
    }

    public function create()
    {
        $userModel   = new \App\Models\UserModel();
        $doctorModel = new \App\Models\DoctorModel();

        return view('admin/appointment/create', [
            'users'   => $userModel->asArray()->findAll(),
            'doctors' => $doctorModel->findAll()
        ]);
    }

    public function store()
    {
        $appointmentModel = new AppointmentModel();

        $appointmentModel->save([
            'user_id'   => $this->request->getPost('user_id'),
            'doctor_id' => $this->request->getPost('doctor_id'),
            'date'      => $this->request->getPost('date'),
            'time'      => $this->request->getPost('time'),
            'status'    => $this->request->getPost('status'),
        ]);

        return redirect()->to('/admin/appointment')->with('success', 'Data berhasil disimpan');
    }

    public function edit($id)
    {
        $appointmentModel = new AppointmentModel();
        $userModel        = new \App\Models\UserModel();
        $doctorModel      = new \App\Models\DoctorModel();

        return view('admin/appointment/edit', [
            'appointment' => $appointmentModel->find($id),
            'users'       => $userModel->asArray()->findAll(),
            'doctors'     => $doctorModel->findAll()
        ]);
    }

    public function update($id)
    {
        $appointmentModel = new AppointmentModel();
        $queueModel       = new QueueModel();

        $appointment = $appointmentModel->find($id);

        $appointmentModel->update($id, [
            'user_id'   => $this->request->getPost('user_id'),
            'doctor_id' => $this->request->getPost('doctor_id'),
            'date'      => $this->request->getPost('date'),
            'time'      => $this->request->getPost('time'),
            'status'    => $this->request->getPost('status'),
        ]);

        $newStatus = $this->request->getPost('status');

        if (($newStatus === 'confirmed' || $newStatus === 'done') && $appointment['no_antrian'] == null) {

            $last = $appointmentModel
                ->where('date', $appointment['date'])
                ->selectMax('no_antrian')
                ->first();

            $queueNumber = ($last['no_antrian'] ?? 0) + 1;

            $appointmentModel->update($id, [
                'no_antrian' => $queueNumber
            ]);

            $queueModel->insert([
                'patient_name' => $appointment['patient_name'] ?? 'Pasien',
                'service'      => 'Konsultasi',
                'queue_number' => $queueNumber
            ]);
        }

        return redirect()->to('/admin/appointment')->with('success', 'Data berhasil diupdate');
    }

    public function delete($id)
    {
        $appointmentModel = new AppointmentModel();
        $appointmentModel->delete($id);

        return redirect()->to('/admin/appointment')->with('success', 'Data berhasil dihapus');
    }
}

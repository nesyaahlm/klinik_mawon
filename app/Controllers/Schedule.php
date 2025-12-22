<?php

namespace App\Controllers;

use App\Models\AppointmentModel; 

class Schedule extends BaseController
{

    public function index($doctorId)
    {
        $doctors = [
            1 => ['name' => 'Dr. Ahmad Sutanto, Sp.PD'],
            2 => ['name' => 'Dr. Siti Nurhaliza, Sp.A'],
            3 => ['name' => 'Dr. Budi Santoso, Sp.JP'],
        ];

        if (!isset($doctors[$doctorId])) {
            return redirect()->to('/doctors');
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
            'doctor' => $doctors[$doctorId],
            'id' => $doctorId,
            'scheduleTimes' => $scheduleTimes
        ]);
    }

public function process()
{
    $appointmentModel = new AppointmentModel();

    $appointmentModel->insert([
        'user_id'   => user()->id,
        'doctor_id' => $this->request->getPost('doctor_id'),
        'date'      => $this->request->getPost('date'),
        'time'      => $this->request->getPost('time'),
    ]);


    $appointmentId = $appointmentModel->getInsertID();
    
    return redirect()->to('/confirmation/' . $appointmentId);
}



}

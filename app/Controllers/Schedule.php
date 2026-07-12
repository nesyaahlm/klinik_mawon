<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use App\Models\DoctorModel;
use App\Models\DoctorScheduleModel;

class Schedule extends BaseController
{

    public function index($doctorId)
    {
        $doctor = (new DoctorModel())->find((int) $doctorId);
        if ($doctor === null) {
            return redirect()->to('/doctors');
        }

        $scheduleTimes = $this->scheduleTimes((int) $doctorId);

        return view('schedule', [
            'doctor' => $doctor,
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
        'status'    => 'pending',
    ]);


    $appointmentId = $appointmentModel->getInsertID();
    
    return redirect()->to('/confirmation/' . $appointmentId);
}

private function scheduleTimes(int $doctorId): array
{
    try {
        if (! db_connect()->tableExists('doctor_schedule')) {
            return [];
        }

        $schedules = (new DoctorScheduleModel())
            ->where('doctor_id', $doctorId)
            ->findAll();

        $times = [];
        foreach ($schedules as $schedule) {
            if (! empty($schedule['start_time'])) {
                $times[] = substr((string) $schedule['start_time'], 0, 5);
            }
        }

        return array_values(array_unique($times));
    } catch (\Throwable) {
        return [];
    }
}



}

<?php

namespace App\Controllers\Api;

use App\Controllers\RestfullController;
use Myth\Auth\Models\UserModel;
use App\Models\AppointmentModel;
use App\Models\DoctorModel;
use App\Models\KontakModel;

class Dashboard extends RestfullController
{
    public function index()
    {
        $userModel        = new UserModel();
        $appointmentModel = new AppointmentModel();
        $doctorModel      = new DoctorModel();
        $kontakModel      = new KontakModel();

        $data = [
            'total_users'        => $userModel->countAll(),
            'total_doctors'      => $doctorModel->countAll(),
            'today_appointments' => $appointmentModel
                                        ->where('date', date('Y-m-d'))
                                        ->countAllResults(),
            'total_contacts'     => $kontakModel->countAllResults(),
            'latest_contacts'    => $kontakModel
                                        ->orderBy('created_at', 'DESC')
                                        ->findAll(5)
        ];

        return $this->responseHasil(200, true, $data);
    }
}
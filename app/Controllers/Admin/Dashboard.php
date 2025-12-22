<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use Myth\Auth\Models\UserModel;
use App\Models\AppointmentModel;
use App\Models\DoctorModel;
use App\Models\KontakModel; 
class Dashboard extends BaseController
{
    public function index()
    {
        if (!in_groups('admin')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $userModel = new UserModel();
        $appointmentModel = new AppointmentModel();
        $doctorModel = new DoctorModel();
        $kontakModel = new KontakModel(); 

        $data = [
            'total_users'         => $userModel->countAll(),
            'total_doctors'       => $doctorModel->countAll(),
            'today_appointments'  => $appointmentModel->where('date', date('Y-m-d'))->countAllResults(),
            'total_contacts'      => $kontakModel->countAllResults(), 
            'latest_contacts'     => $kontakModel->orderBy('created_at', 'DESC')->findAll(5) 
        ];

        return view('admin/dashboard', $data);
    }
}

<?php

namespace App\Controllers;

use App\Models\DoctorModel;

class Doctors extends BaseController
{
    public function index()
    {
        $doctorModel = new DoctorModel();
        $data['doctors'] = $doctorModel->findAll();

        return view('doctors', $data);
    }
}

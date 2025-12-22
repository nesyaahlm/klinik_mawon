<?php

namespace App\Controllers;

class Klinik extends BaseController
{
    public function services()
    {
        return view('services');
    }

    public function doctors()
    {
        return view('doctors');
    }

    public function schedule()
    {
        $doctor = $this->request->getPost('doctor');

        return view('schedule', [
            'doctor' => $doctor
        ]);
    }

    public function confirmation()
    {
        return view('confirmation', [
            'doctor' => $this->request->getPost('doctor'),
            'date'   => $this->request->getPost('date'),
            'time'   => $this->request->getPost('time'),
        ]);
    }
}

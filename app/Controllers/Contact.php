<?php

namespace App\Controllers;

use App\Models\KontakModel;

class Contact extends BaseController
{
    public function index()
    {
        return view('contact');
    }

    public function send()
    {
        $model = new KontakModel();

        $data = [
            'nama' => $this->request->getPost('nama'),
            'email' => $this->request->getPost('email'),
            'pesan' => $this->request->getPost('pesan')
        ];

        $model->insert($data);

        return redirect()->back()->with('success', 'Pesan berhasil dikirim!');
    }
}

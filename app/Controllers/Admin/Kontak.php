<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\KontakModel;

class Kontak extends BaseController
{
    public function index()
    {
        if (!in_groups('admin')) {
            return redirect()->to('/')->with('error', 'Akses ditolak.');
        }

        $kontakModel = new KontakModel();

        $data = [
            'contacts' => $kontakModel->orderBy('created_at', 'DESC')->findAll()
        ];

        return view('admin/kontak/index', $data);
    }
}

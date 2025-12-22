<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DoctorModel;

class AdminDoctor extends BaseController
{
    protected $doctorModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
    }

    public function index()
    {
        $data['doctors'] = $this->doctorModel->findAll();
        return view('admin/dokter/index', $data);
    }

    public function create()
    {
        return view('admin/dokter/create');
    }

    public function store()
    {
        $this->doctorModel->save([
            'name'           => $this->request->getPost('name'),
            'specialization' => $this->request->getPost('specialization'),
            'phone'          => $this->request->getPost('phone'),
            'email'          => $this->request->getPost('email'),
        ]);

        return redirect()->to('admin/dokter')->with('message', 'Dokter berhasil ditambahkan');
    }

    public function edit($id)
    {
        $data['doctor'] = $this->doctorModel->find($id);
        return view('admin/dokter/edit', $data);
    }

    public function update($id)
    {
        $this->doctorModel->update($id, [
            'name'           => $this->request->getPost('name'),
            'specialization' => $this->request->getPost('specialization'),
            'phone'          => $this->request->getPost('phone'),
            'email'          => $this->request->getPost('email'),
        ]);

        return redirect()->to('admin/dokter')->with('message', 'Dokter berhasil diupdate');
    }

    public function delete($id)
    {
        $this->doctorModel->delete($id);
        return redirect()->to('admin/dokter')->with('message', 'Dokter berhasil dihapus');
    }
}

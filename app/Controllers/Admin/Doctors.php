<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\DoctorModel;

class Doctors extends BaseController
{
    protected $doctorModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
    }

    public function index()
    {
        $data['doctors'] = $this->doctorModel->findAll();
        return view('admin/doctors/index', $data);
    }

    public function create()
    {
        return view('admin/doctors/create');
    }

    public function store()
    {
        $photo = $this->request->getFile('photo');
        $fileName = null;

        if($photo && $photo->isValid() && !$photo->hasMoved()){
            $fileName = $photo->getRandomName();
            $photo->move('img', $fileName);
        }

        $this->doctorModel->save([
            'name'           => $this->request->getPost('name'),
            'specialization' => $this->request->getPost('specialization'),
            'phone'          => $this->request->getPost('phone'),
            'email'          => $this->request->getPost('email'),
            'photo'          => $fileName
        ]);

        return redirect()->to('admin/dokter');
    }

    public function edit($id)
    {
        $data['doctor'] = $this->doctorModel->find($id);
        return view('admin/doctors/edit', $data);
    }

    public function update($id)
    {
        $doctor = $this->doctorModel->find($id);
        $dataUpdate = [
            'name'           => $this->request->getPost('name'),
            'specialization' => $this->request->getPost('specialization'),
            'phone'          => $this->request->getPost('phone'),
            'email'          => $this->request->getPost('email')
        ];

        $photo = $this->request->getFile('photo');
        if($photo && $photo->isValid() && !$photo->hasMoved()){
            $fileName = $photo->getRandomName();
            $photo->move('img', $fileName);
            $dataUpdate['photo'] = $fileName;
        }

        $this->doctorModel->update($id, $dataUpdate);
        return redirect()->to('admin/dokter');
    }

    public function delete($id)
    {
        $this->doctorModel->delete($id);
        return redirect()->to('admin/dokter');
    }
}

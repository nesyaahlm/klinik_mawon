<?php

namespace App\Controllers\Api;

use App\Controllers\RestfullController;
use App\Models\DoctorModel;

class Doctors extends RestfullController
{
    protected $doctorModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
    }

    // GET ALL DATA
    public function index()
    {
        $doctors = $this->doctorModel->findAll();

        return $this->responseHasil(200, true, $doctors);
    }

    // GET DETAIL
    public function show($id = null)
    {
        $doctor = $this->doctorModel->find($id);

        if (!$doctor) {
            return $this->responseHasil(404, false, 'Dokter tidak ditemukan');
        }

        return $this->responseHasil(200, true, $doctor);
    }

    // CREATE DATA
    public function create()
    {
        $photo = $this->request->getFile('photo');
        $fileName = null;

        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $fileName = $photo->getRandomName();
            $photo->move('img', $fileName);
        }

        $data = [
            'name'           => $this->request->getPost('name'),
            'specialization' => $this->request->getPost('specialization'),
            'phone'          => $this->request->getPost('phone'),
            'email'          => $this->request->getPost('email'),
            'photo'          => $fileName
        ];

        $this->doctorModel->insert($data);

        return $this->responseHasil(201, true, 'Dokter berhasil ditambahkan');
    }

    // UPDATE DATA
    public function update($id = null)
    {
        $doctor = $this->doctorModel->find($id);

        if (!$doctor) {
            return $this->responseHasil(404, false, 'Dokter tidak ditemukan');
        }

        $dataUpdate = [
            'name'           => $this->request->getPost('name') ?? $doctor['name'],
            'specialization' => $this->request->getPost('specialization') ?? $doctor['specialization'],
            'phone'          => $this->request->getPost('phone') ?? $doctor['phone'],
            'email'          => $this->request->getPost('email') ?? $doctor['email']
        ];

        $photo = $this->request->getFile('photo');

        if ($photo && $photo->isValid() && !$photo->hasMoved()) {

            $fileName = $photo->getRandomName();

            $photo->move('img', $fileName);

            $dataUpdate['photo'] = $fileName;
        }

        $this->doctorModel->update($id, $dataUpdate);

        return $this->responseHasil(200, true, 'Dokter berhasil diupdate');
    }

    // DELETE DATA
    public function delete($id = null)
    {
        $doctor = $this->doctorModel->find($id);

        if (!$doctor) {
            return $this->responseHasil(404, false, 'Dokter tidak ditemukan');
        }

        $this->doctorModel->delete($id);

        return $this->responseHasil(200, true, 'Dokter berhasil dihapus');
    }
}
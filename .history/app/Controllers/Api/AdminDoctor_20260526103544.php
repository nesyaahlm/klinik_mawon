<?php

namespace App\Controllers\Api;

use App\Controllers\RestfullController;
use App\Models\DoctorModel;

class AdminDoctor extends RestfullController
{
    protected $doctorModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
    }

    // GET ALL DATA
    public function index()
    {
        $data = $this->doctorModel->findAll();

        return $this->responseHasil(200, true, $data);
    }

    // GET DETAIL DATA
    public function show($id = null)
    {
        $doctor = $this->doctorModel->find($id);

        if (!$doctor) {
            return $this->responseHasil(404, false, 'Data dokter tidak ditemukan');
        }

        return $this->responseHasil(200, true, $doctor);
    }

    // POST DATA
    public function create()
    {
        $input = $this->request->getJSON(true);

        $data = [
            'name' => $input['name'] ?? null,
            'specialization' => $input['specialization'] ?? null,
            'phone' => $input['phone'] ?? null,
            'email' => $input['email'] ?? null,
        ];

        $this->doctorModel->insert($data);

        return $this->responseHasil(201, true, 'Dokter berhasil ditambahkan');
    }

    // UPDATE DATA
    public function update($id = null)
    {
        $doctor = $this->doctorModel->find($id);

        if (!$doctor) {
            return $this->responseHasil(404, false, 'Data dokter tidak ditemukan');
        }

        $input = $this->request->getRawInput();
        $data = [
            'name' => $input['name'] ?? $doctor['name'],
            'specialization' => $input['specialization'] ?? $doctor['specialization'],
            'phone' => $input['phone'] ?? $doctor['phone'],
            'email' => $input['email'] ?? $doctor['email'],
        ];

        $this->doctorModel->update($id, $data);

        return $this->responseHasil(200, true, 'Dokter berhasil diupdate');
    }

    // DELETE DATA
    public function delete($id = null)
    {
        $doctor = $this->doctorModel->find($id);

        if (!$doctor) {
            return $this->responseHasil(404, false, 'Data dokter tidak ditemukan');
        }

        $this->doctorModel->delete($id);

        return $this->responseHasil(200, true, 'Dokter berhasil dihapus');
    }
}
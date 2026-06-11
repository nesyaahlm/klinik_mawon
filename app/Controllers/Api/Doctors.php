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

        foreach ($doctors as &$doctor) {
            if (!empty($doctor['photo'])) {
                $doctor['photo'] = base_url('img/' . $doctor['photo']);
            }
        }

        return $this->responseHasil(200, true, $doctors);
    }

    // GET DETAIL
    public function show($id = null)
    {
        $doctor = $this->doctorModel->find($id);

        if (!$doctor) {
            return $this->responseHasil(404, false, 'Dokter tidak ditemukan');
        }

        if (!empty($doctor['photo'])) {
            $doctor['photo'] = base_url('img/' . $doctor['photo']);
        }

        return $this->responseHasil(200, true, $doctor);
    }

    // CREATE DATA
    public function create()
    {
        $input = $this->requestInput();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

        $photo = $this->request->getFile('photo');
        $fileName = null;

        if ($photo && $photo->isValid() && !$photo->hasMoved()) {
            $fileName = $photo->getRandomName();
            $photo->move('img', $fileName);
        }

        $data = [
            'name'           => $input['name'] ?? null,
            'specialization' => $input['specialization'] ?? null,
            'phone'          => $input['phone'] ?? null,
            'email'          => $input['email'] ?? null,
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

        $input = $this->requestInput();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

        $dataUpdate = [
            'name'           => $input['name'] ?? $doctor['name'],
            'specialization' => $input['specialization'] ?? $doctor['specialization'],
            'phone'          => $input['phone'] ?? $doctor['phone'],
            'email'          => $input['email'] ?? $doctor['email']
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

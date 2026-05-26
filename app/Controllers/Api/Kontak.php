<?php

namespace App\Controllers\Api;

use App\Controllers\RestfullController;
use App\Models\KontakModel;

class Kontak extends RestfullController
{
    protected $kontakModel;

    public function __construct()
    {
        $this->kontakModel = new KontakModel();
    }

    // GET ALL KONTAK
    public function index()
    {
        $contacts = $this->kontakModel
            ->orderBy('created_at', 'DESC')
            ->findAll();

        return $this->responseHasil(200, true, $contacts);
    }

    // GET DETAIL KONTAK
    public function show($id = null)
    {
        $contact = $this->kontakModel->find($id);

        if (!$contact) {
            return $this->responseHasil(404, false, 'Kontak tidak ditemukan');
        }

        return $this->responseHasil(200, true, $contact);
    }

    // CREATE KONTAK
    public function create()
    {
        $input = $this->request->getJSON(true);

        $data = [
            'name'    => $input['name'] ?? null,
            'email'   => $input['email'] ?? null,
            'subject' => $input['subject'] ?? null,
            'message' => $input['message'] ?? null,
        ];

        $this->kontakModel->insert($data);

        return $this->responseHasil(201, true, 'Pesan kontak berhasil ditambahkan');
    }

    // DELETE KONTAK
    public function delete($id = null)
    {
        $contact = $this->kontakModel->find($id);

        if (!$contact) {
            return $this->responseHasil(404, false, 'Kontak tidak ditemukan');
        }

        $this->kontakModel->delete($id);

        return $this->responseHasil(200, true, 'Kontak berhasil dihapus');
    }
}
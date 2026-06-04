<?php

namespace App\Controllers\Api;

use App\Models\DoctorModel;
use App\Models\DoctorScheduleModel;

class DoctorController extends BaseApiController
{
    public function index()
    {
        $model = new DoctorModel();
        $specialization = $this->request->getGet('specialization');

        if ($specialization !== null && $specialization !== '') {
            $model->like('specialization', $specialization);
        }

        return $this->success($model->findAll());
    }

    public function show(int $id)
    {
        $doctor = (new DoctorModel())->find($id);

        if ($doctor === null) {
            return $this->failure('Dokter tidak ditemukan.', [], 404);
        }

        return $this->success($doctor);
    }

    public function schedules(int $id)
    {
        if ((new DoctorModel())->find($id) === null) {
            return $this->failure('Dokter tidak ditemukan.', [], 404);
        }

        $schedules = (new DoctorScheduleModel())
            ->where('doctor_id', $id)
            ->orderBy('id', 'ASC')
            ->findAll();

        return $this->success($schedules);
    }
}

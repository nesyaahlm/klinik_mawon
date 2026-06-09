<?php

namespace App\Controllers\Api;

use App\Models\DoctorModel;
use App\Models\DoctorScheduleModel;
use Throwable;

class DoctorController extends BaseApiController
{
    public function index()
    {
        $model = new DoctorModel();
        $specialization = $this->request->getGet('specialization');

        if ($specialization !== null && $specialization !== '') {
            $model->like('specialization', $specialization);
        }

        $doctors = array_map(fn (array $doctor): array => $this->formatDoctor($doctor), $model->findAll());

        return $this->success($doctors, 'Data dokter berhasil diambil.');
    }

    public function show(int $id)
    {
        $doctor = (new DoctorModel())->find($id);

        if ($doctor === null) {
            return $this->failure('Dokter tidak ditemukan.', [], 404);
        }

        return $this->success($this->formatDoctor($doctor), 'Data dokter berhasil diambil.');
    }

    public function schedules(int $id)
    {
        if ((new DoctorModel())->find($id) === null) {
            return $this->failure('Dokter tidak ditemukan.', [], 404);
        }

        try {
            if (! db_connect()->tableExists('doctor_schedule')) {
                return $this->success([], 'Jadwal dokter belum tersedia.');
            }

            $schedules = (new DoctorScheduleModel())
                ->where('doctor_id', $id)
                ->orderBy('id', 'ASC')
                ->findAll();
        } catch (Throwable) {
            return $this->success([], 'Jadwal dokter belum tersedia.');
        }

        return $this->success($schedules, 'Data jadwal berhasil diambil.');
    }

    public function allSchedules()
    {
        try {
            $db = db_connect();

            if (! $db->tableExists('doctor_schedule')) {
                return $this->success([], 'Jadwal dokter belum tersedia.');
            }

            $schedules = (new DoctorScheduleModel())
                ->select('doctor_schedule.*, doctors.name AS doctor_name, doctors.specialization')
                ->join('doctors', 'doctors.id = doctor_schedule.doctor_id', 'left')
                ->orderBy('doctor_schedule.doctor_id', 'ASC')
                ->orderBy('doctor_schedule.id', 'ASC')
                ->findAll();
        } catch (Throwable) {
            return $this->success([], 'Jadwal dokter belum tersedia.');
        }

        return $this->success($schedules, 'Data jadwal berhasil diambil.');
    }

    private function formatDoctor(array $doctor): array
    {
        $schedule = $doctor['schedule'] ?? '';
        $photo = $doctor['photo'] ?? '';

        return array_merge($doctor, [
            'id_dokter'       => (int) ($doctor['id'] ?? 0),
            'nama'            => $doctor['name'] ?? '',
            'nama_dokter'     => $doctor['name'] ?? '',
            'specialty'       => $doctor['specialization'] ?? '',
            'spesialis'       => $doctor['specialization'] ?? '',
            'polyclinic'      => $doctor['polyclinic'] ?? 'Poli',
            'poli'            => $doctor['polyclinic'] ?? 'Poli',
            'practice_time'   => $schedule,
            'jadwal_praktik'  => $schedule,
            'image_url'       => $photo,
            'foto'            => $photo,
            'available_dates' => ['Hari ini'],
            'available_times' => $this->extractTimes($schedule),
            'fee'             => (int) ($doctor['fee'] ?? $doctor['tarif'] ?? 0),
        ]);
    }

    private function extractTimes(string $schedule): array
    {
        preg_match_all('/\d{1,2}[.:]\d{2}/', $schedule, $matches);

        if (empty($matches[0])) {
            return ['08:00'];
        }

        return array_map(static fn (string $time): string => str_replace('.', ':', $time), $matches[0]);
    }
}

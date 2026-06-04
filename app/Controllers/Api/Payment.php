<?php

namespace App\Controllers\Api;

use App\Controllers\RestfullController;
use App\Models\AppointmentModel;
use App\Models\PaymentModel;

class Payment extends RestfullController
{
    protected $appointmentModel;
    protected $paymentModel;

    public function __construct()
    {
        $this->appointmentModel = new AppointmentModel();
        $this->paymentModel = new PaymentModel();
    }

    public function create()
    {
        $input = $this->request->getJSON(true) ?? [];
        $appointmentId = $input['booking_id'] ?? $input['id_booking'] ?? null;

        if (! is_numeric($appointmentId)) {
            return $this->responseHasil(400, false, 'booking_id wajib berupa ID appointment');
        }

        $appointment = $this->appointmentModel->find((int) $appointmentId);
        if (! $appointment) {
            return $this->responseHasil(404, false, 'Appointment tidak ditemukan');
        }

        $this->paymentModel->insert([
            'appointment_id' => (int) $appointmentId,
            'payment_method' => $input['payment_method'] ?? $input['metode_pembayaran'] ?? 'QRIS',
            'amount' => $input['amount'] ?? $input['total'] ?? 0,
            'status' => 'confirmed',
            'proof' => $input['proof'] ?? null,
            'bukti' => $input['bukti'] ?? null,
        ]);

        $queueNumber = $appointment['no_antrian'];
        if ($queueNumber === null || $queueNumber === '') {
            $last = $this->appointmentModel
                ->where('date', $appointment['date'])
                ->selectMax('no_antrian')
                ->first();

            $queueNumber = ((int) ($last['no_antrian'] ?? 0)) + 1;
        }

        $this->appointmentModel->update((int) $appointmentId, [
            'status' => 'confirmed',
            'no_antrian' => $queueNumber,
        ]);

        $updated = $this->appointmentModel
            ->select('appointments.*, 
                      users.username, 
                      doctors.name AS doctor_name,
                      doctors.specialization,
                      doctors.schedule,
                      payments.payment_method,
                      payments.amount,
                      payments.proof')
            ->join('users', 'users.id = appointments.user_id', 'left')
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->join('payments', 'payments.appointment_id = appointments.id', 'left')
            ->where('appointments.id', (int) $appointmentId)
            ->first();

        return $this->responseHasil(200, true, $updated);
    }
}

<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\AppointmentModel;
use App\Models\QueueModel;

class Payment extends BaseController
{
    public function confirm($appointmentId)
    {
        $paymentModel     = new PaymentModel();
        $appointmentModel = new AppointmentModel();
        $queueModel       = new QueueModel();

        $appointment = $appointmentModel->find($appointmentId);
        $payment     = $paymentModel->where('appointment_id', $appointmentId)->first();

        if (!$appointment || !$payment) {
            return redirect()->back()->with('error', 'Data tidak ditemukan.');
        }

        $paymentModel->update($payment['id'], ['status' => 'done']);

        $last = $appointmentModel
            ->where('date', $appointment['date'])
            ->selectMax('no_antrian')
            ->first();

        $queueNumber = ($last['no_antrian'] ?? 0) + 1;

        $appointmentModel->update($appointmentId, [
            'status' => 'paid',
            'no_antrian' => $queueNumber
        ]);

        $queueModel->insert([
            'patient_name' => $appointment['patient_name'],
            'service'      => 'Konsultasi',
            'queue_number' => $queueNumber
        ]);


        return redirect()->to('/confirmation/' . $appointmentId)
                         ->with('success', 'Pembayaran dikonfirmasi & nomor antrian dibuat.');
    }
}

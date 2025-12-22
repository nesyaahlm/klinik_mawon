<?php

namespace App\Controllers;

use App\Models\PaymentModel;
use App\Models\QueueModel;
use App\Models\AppointmentModel;

class Payment extends BaseController
{
    public function index()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $appointmentId = session('appointment_id');

        if (!$appointmentId) {
            return redirect()->to('/doctors')
                ->with('error', 'Pilih jadwal terlebih dahulu.');
        }

        $appointmentModel = new AppointmentModel();
        $appointment = $appointmentModel->find($appointmentId);

        return view('payment', [
            'appointment_id' => $appointmentId,
            'appointment'    => $appointment
        ]);
    }

    public function process()
    {
        if (!logged_in()) {
            return redirect()->to('/login');
        }

        $appointmentId = session('appointment_id');
        $method        = $this->request->getPost('payment_method');

        if (!$appointmentId) {
            return redirect()->to('/doctors')
                ->with('error', 'Appointment tidak ditemukan.');
        }

        $appointmentModel = new AppointmentModel();
        $paymentModel     = new PaymentModel();

        $appointment = $appointmentModel->find($appointmentId);

        if (!$appointment) {
            return redirect()->to('/doctors')
                ->with('error', 'Data appointment tidak valid.');
        }

        $filename = null;
        if ($method === 'qris') {
            $file = $this->request->getFile('bukti');

            if (!$file || !$file->isValid()) {
                return redirect()->back()
                    ->with('error', 'Bukti pembayaran wajib diupload!');
            }

            $filename = $file->getRandomName();
            $file->move(ROOTPATH . 'public/img', $filename);
        }

        $appointmentModel->update($appointmentId, [
            'status' => 'pending'
        ]);

        $paymentModel->insert([
            'appointment_id'  => $appointmentId,
            'payment_method'  => ($method === 'cod' ? 'cash' : 'qris'),
            'amount'          => 150000,
            'proof'           => $filename,
            'bukti'           => $filename
        ]);

        return redirect()->to('/confirmation/' . $appointmentId)
            ->with('info', 'Pembayaran berhasil diupload. Menunggu konfirmasi admin.');
    }
    
}

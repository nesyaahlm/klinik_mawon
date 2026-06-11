<?php

namespace App\Controllers\Api;

use App\Models\AppointmentModel;
use App\Models\PaymentModel;
use Throwable;

class Payment extends BaseApiController
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
        $input = $this->input();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

        $appointmentId = $input['booking_id'] ?? $input['id_booking'] ?? null;

        if (! is_numeric($appointmentId)) {
            return $this->failure('booking_id wajib berupa ID appointment.', [], 422);
        }

        $appointment = $this->appointmentModel->find((int) $appointmentId);
        if (! $appointment) {
            return $this->failure('Booking tidak ditemukan.', [], 404);
        }

        $paymentMethod = $input['payment_method'] ?? $input['metode_pembayaran'] ?? 'QRIS';
        $amount = $input['amount'] ?? $input['total'] ?? 0;
        $paymentSaved = $this->savePaymentIfTableIsUsable((int) $appointmentId, $paymentMethod, $amount, $input);

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
                      doctors.schedule')
            ->join('users', 'users.id = appointments.user_id', 'left')
            ->join('doctors', 'doctors.id = appointments.doctor_id', 'left')
            ->where('appointments.id', (int) $appointmentId)
            ->first();

        $updated['payment_method'] = $paymentMethod;
        $updated['metode_pembayaran'] = $paymentMethod;
        $updated['amount'] = $amount;
        $updated['total'] = (int) $amount;
        $updated['payment_saved'] = $paymentSaved;
        $updated['queue_number'] = $updated['no_antrian'] ?? null;
        $updated['nomor_antrian'] = $updated['no_antrian'] ?? null;
        $updated['code'] = 'KM-' . (int) $appointmentId;
        $updated['booking_code'] = $updated['code'];
        $updated['kode_booking'] = $updated['code'];

        return $this->success($updated, 'Pembayaran berhasil dikonfirmasi.');
    }

    private function savePaymentIfTableIsUsable(int $appointmentId, string $paymentMethod, $amount, array $input): bool
    {
        try {
            $db = db_connect();
            if (! $db->tableExists('payments')) {
                return false;
            }

            $paymentData = [
                'appointment_id' => $appointmentId,
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'status' => 'confirmed',
                'proof' => $input['proof'] ?? null,
                'bukti' => $input['bukti'] ?? null,
            ];

            $paymentData = array_filter(
                $paymentData,
                static fn ($value, string $field): bool => $db->fieldExists($field, 'payments'),
                ARRAY_FILTER_USE_BOTH
            );

            if ($paymentData === []) {
                return false;
            }

            return (bool) $this->paymentModel->insert($paymentData);
        } catch (Throwable) {
            return false;
        }
    }
}

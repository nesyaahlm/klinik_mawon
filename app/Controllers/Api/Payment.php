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

        $appointmentId = $input['booking_id'] ?? $input['id_booking'] ?? $input['appointment_id'] ?? $input['id_appointment'] ?? null;

        if (! is_numeric($appointmentId)) {
            return $this->failure('booking_id atau appointment_id wajib berupa ID appointment.', [], 422);
        }

        $userId = $this->currentUserId();
        if ($userId === null) {
            return $this->failure('Token autentikasi diperlukan.', [], 401);
        }

        $appointment = $this->appointmentModel->find((int) $appointmentId);
        if (! $appointment) {
            return $this->failure('Booking tidak ditemukan.', [], 404);
        }
        if ((int) ($appointment['user_id'] ?? 0) !== $userId) {
            return $this->failure('Booking tidak ditemukan untuk akun aktif.', [], 404);
        }

        $paymentMethod = strtolower($input['payment_method'] ?? $input['metode_pembayaran'] ?? 'qris');
        if ($paymentMethod === 'transfer') {
            $paymentMethod = 'bank_transfer';
        }
        $amount = $input['amount'] ?? $input['total'] ?? 0;

        if (! in_array($paymentMethod, ['cash', 'bank_transfer', 'qris'], true)) {
            return $this->failure('Metode pembayaran tidak valid.', [], 422);
        }

        if (! is_numeric($amount) || (int) $amount <= 0) {
            return $this->failure('Jumlah pembayaran harus lebih besar dari nol.', [], 422);
        }

        $status = strtolower($appointment['status'] ?? '');
        if (in_array($status, ['paid', 'done', 'completed', 'cancelled', 'canceled'], true)) {
            return $this->failure('Booking sudah dikonfirmasi dan tidak dapat dibayar lagi.', [], 422);
        }

        if ($status === 'cancelled' || $status === 'dibatalkan' || $status === 'canceled') {
            return $this->failure('Booking yang dibatalkan tidak dapat dibayar.', [], 422);
        }

        $paymentStatus = $paymentMethod === 'cash' ? 'pay_at_clinic' : 'pending';
        $appointmentStatus = $paymentMethod === 'cash' ? 'confirmed' : 'menunggu konfirmasi';

        $paymentSaved = $this->savePaymentIfTableIsUsable((int) $appointmentId, $paymentMethod, $amount, $input, $paymentStatus);

        $queueNumber = $appointment['no_antrian'];
        if ($queueNumber === null || $queueNumber === '') {
            $last = $this->appointmentModel
                ->where('date', $appointment['date'])
                ->selectMax('no_antrian')
                ->first();

            $queueNumber = ((int) ($last['no_antrian'] ?? 0)) + 1;
        }

        $this->appointmentModel->update((int) $appointmentId, [
            'status' => $appointmentStatus,
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

    private function savePaymentIfTableIsUsable(int $appointmentId, string $paymentMethod, $amount, array $input, string $paymentStatus): bool
    {
        try {
            $db = db_connect();
            $table = $this->safeTableExists('payments') ? 'payments' : 'mobile_payments';
            if (! $this->safeTableExists($table)) {
                return false;
            }

            $paymentData = [
                'appointment_id' => $appointmentId,
                'payment_method' => $paymentMethod,
                'amount' => $amount,
                'status' => $paymentStatus,
                'proof' => $input['proof'] ?? null,
                'bukti' => $input['bukti'] ?? null,
            ];

            $paymentData = array_filter(
                $paymentData,
                fn ($value, string $field): bool => $this->safeFieldExists($field, $table),
                ARRAY_FILTER_USE_BOTH
            );

            if ($paymentData === []) {
                return false;
            }

            return (bool) $db->table($table)->insert($paymentData);
        } catch (Throwable) {
            return false;
        }
    }
}

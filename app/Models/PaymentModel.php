<?php

namespace App\Models;
use CodeIgniter\Model;

class PaymentModel extends Model
{
    protected $table = 'payments';
    protected $primaryKey = 'id';

   protected $allowedFields = [
    'appointment_id',
    'payment_method',
    'amount',
    'payment_date',
    'status',
    'proof',
    'bukti'
];

    
    protected $useTimestamps = false;
}

<?php

namespace App\Models;

use CodeIgniter\Model;

class AppointmentModel extends Model
{
    protected $table = 'appointments';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'user_id',
        'doctor_id',
        'date',
        'time',
        'schedule_id',
        'keluhan',
        'payment_method',
        'amount',
        'no_antrian',
        'status',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
}

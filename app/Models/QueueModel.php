<?php

namespace App\Models;
use CodeIgniter\Model;

class QueueModel extends Model
{
    protected $table = 'queue';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'appointment_id',
        'queue_number',
        'status'
    ];

    
    protected $useTimestamps = false;
}

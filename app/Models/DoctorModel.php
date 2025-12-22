<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorModel extends Model
{
    protected $table      = 'doctors';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'name', 'specialization', 'phone', 'email', 'photo', 'schedule'
    ];

    protected $useTimestamps = true;
}

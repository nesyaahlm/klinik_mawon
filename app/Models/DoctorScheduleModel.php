<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorScheduleModel extends Model
{
    protected $table = 'doctor_schedule';
    protected $primaryKey = 'id';
    protected $allowedFields = ['doctor_id', 'day', 'date', 'start_time', 'end_time', 'is_available'];
    protected $returnType = 'array';

}

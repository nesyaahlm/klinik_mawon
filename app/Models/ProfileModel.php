<?php

namespace App\Models;

use CodeIgniter\Model;

class ProfileModel extends Model
{
    protected $table            = 'profiles';
    protected $primaryKey       = 'id';
    protected $allowedFields    = ['user_id', 'name', 'phone', 'address'];
    protected $useTimestamps    = true;
    protected $createdField    = 'created_at';
    protected $updatedField    = 'updated_at';
}

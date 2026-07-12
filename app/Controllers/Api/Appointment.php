<?php

namespace App\Controllers\Api;

class Appointment extends AppointmentController
{
    public function delete($id = null)
    {
        return $this->cancel((int) $id);
    }
}

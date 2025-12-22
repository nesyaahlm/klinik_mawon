<?php

namespace App\Controllers;

use App\Models\QueueModel;

class Queue extends BaseController
{
    public function index()
    {
        $queueNumber = session()->get('queue_number') ?? null;
        return view('queue', ['queue_number' => $queueNumber]);
    }
}

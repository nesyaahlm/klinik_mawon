<?php

namespace App\Controllers\Api;

class ErrorController extends BaseApiController
{
    public function notFound()
    {
        return $this->failure('Endpoint API tidak ditemukan.', [], 404);
    }
}

<?php

namespace App\Controllers;

use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;

class RestfullController extends ResourceController
{
    protected $format = 'json';
    private bool $invalidJsonInput = false;

    protected function responseHasil($code, $status, $data)
    {
        $isSuccess = (bool) $status;
        $message = $isSuccess ? 'Berhasil' : 'Gagal';
        $payload = $data;

        if (is_string($data)) {
            $message = $data;
            $payload = null;
        }

        return $this->respond([
            'success' => $isSuccess,
            'message' => $message,
            'data' => $payload,
        ], $code);
    }

    protected function requestInput(): array
    {
        if (str_contains($this->request->getHeaderLine('Content-Type'), 'application/json')) {
            try {
                return $this->request->getJSON(true) ?? [];
            } catch (\Throwable) {
                $this->invalidJsonInput = true;

                return [];
            }
        }

        return $this->request->getPost() ?: $this->request->getRawInput();
    }

    protected function invalidJsonResponse(): ?ResponseInterface
    {
        if (! $this->invalidJsonInput) {
            return null;
        }

        return $this->respond([
            'success' => false,
            'message' => 'Format JSON tidak valid.',
            'data' => null,
            'errors' => [
                'body' => 'Request body harus berupa JSON yang valid.',
            ],
        ], 400);
    }
}

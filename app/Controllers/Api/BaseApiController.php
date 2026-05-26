<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\ApiTokenModel;
use CodeIgniter\HTTP\ResponseInterface;

abstract class BaseApiController extends BaseController
{
    private bool $invalidJsonInput = false;

    protected function success($data = [], string $message = 'Berhasil', int $status = 200): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON([
            'success' => true,
            'message' => $message,
            'data'    => $data,
        ]);
    }

    protected function failure(string $message, array $errors = [], int $status = 400): ResponseInterface
    {
        return $this->response->setStatusCode($status)->setJSON([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
        ]);
    }

    protected function input(): array
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

        return $this->failure('Format JSON tidak valid.', [
            'body' => 'Request body harus berupa JSON yang valid.',
        ], 400);
    }

    protected function bearerToken(): ?string
    {
        if (! preg_match('/^Bearer\s+(\S+)$/i', $this->request->getHeaderLine('Authorization'), $matches)) {
            return null;
        }

        return $matches[1];
    }

    protected function currentUserId(): ?int
    {
        $plainToken = $this->bearerToken();

        if ($plainToken === null) {
            return null;
        }

        $token = (new ApiTokenModel())->findActiveToken($plainToken);

        return $token === null ? null : (int) $token['user_id'];
    }

    protected function publicUser(object $user): array
    {
        return [
            'id'       => (int) $user->id,
            'username' => $user->username,
            'email'    => $user->email,
        ];
    }
}

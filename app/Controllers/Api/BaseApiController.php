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
        return $this->response
            ->setStatusCode($status)
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setJSON([
                'success' => true,
                'message' => $message,
                'data'    => $data,
            ]);
    }

    protected function failure(string $message, array $errors = [], int $status = 400): ResponseInterface
    {
        return $this->response
            ->setStatusCode($status)
            ->setHeader('Access-Control-Allow-Origin', '*')
            ->setHeader('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, PATCH, OPTIONS')
            ->setHeader('Access-Control-Allow-Headers', 'Content-Type, Authorization')
            ->setJSON([
                'success' => false,
                'message' => $message,
                'data'    => null,
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

    /**
     * Normalize a photo path or URL into an absolute URL using base_url().
     * Returns empty string when input is empty.
     */
    protected function normalizePhotoUrl(?string $photo): string
    {
        $photo = trim((string) ($photo ?? ''));
        if ($photo === '') {
            return '';
        }

        if (preg_match('/^https?:\/\//i', $photo)) {
            return $photo;
        }

        // If starts with slash, treat as absolute from site root
        if (str_starts_with($photo, '/')) {
            return base_url($photo);
        }

        // If looks like uploads/... or img/... or contains a directory, return base_url + path
        if (str_contains($photo, '/') || str_starts_with($photo, 'uploads') || str_starts_with($photo, 'img')) {
            return base_url($photo);
        }

        // Fallback: assume image lives under img/
        return base_url('img/' . $photo);
    }

    protected function safeTableExists(string $table): bool
    {
        try {
            if (! db_connect()->tableExists($table)) {
                return false;
            }
            db_connect()->getFieldNames($table);
            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    protected function safeFieldExists(string $field, string $table): bool
    {
        try {
            return $this->safeTableExists($table) && db_connect()->fieldExists($field, $table);
        } catch (\Throwable) {
            return false;
        }
    }

    protected function filterExistingFields(string $table, array $data): array
    {
        return array_filter(
            $data,
            fn ($value, string $field): bool => $value !== null && $this->safeFieldExists($field, $table),
            ARRAY_FILTER_USE_BOTH
        );
    }
}

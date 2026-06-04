<?php

namespace App\Filters;

use App\Models\ApiTokenModel;
use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ApiAuthFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $header = $request->getHeaderLine('Authorization');

        if (! preg_match('/^Bearer\s+(\S+)$/i', $header, $matches)) {
            return $this->unauthorized('Token autentikasi diperlukan.');
        }

        $tokenModel = new ApiTokenModel();
        $token = $tokenModel->findActiveToken($matches[1]);

        if ($token === null) {
            return $this->unauthorized('Token autentikasi tidak valid atau sudah kedaluwarsa.');
        }

        $tokenModel->markUsed((int) $token['id']);

        return null;
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
    }

    private function unauthorized(string $message): ResponseInterface
    {
        return service('response')->setStatusCode(401)->setJSON([
            'success' => false,
            'message' => $message,
            'errors'  => [],
        ]);
    }
}

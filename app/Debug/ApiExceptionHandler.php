<?php

namespace App\Debug;

use CodeIgniter\Debug\ExceptionHandler;
use CodeIgniter\Debug\ExceptionHandlerInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Config\Exceptions;
use Throwable;

class ApiExceptionHandler implements ExceptionHandlerInterface
{
    public function __construct(private Exceptions $config)
    {
    }

    public function handle(
        Throwable $exception,
        RequestInterface $request,
        ResponseInterface $response,
        int $statusCode,
        int $exitCode,
    ): void {
        $path = trim($request->getPath(), '/');

        if ($path !== 'api' && ! str_starts_with($path, 'api/')) {
            (new ExceptionHandler($this->config))->handle($exception, $request, $response, $statusCode, $exitCode);

            return;
        }

        if ($statusCode < 400 || $statusCode > 599) {
            $statusCode = 500;
        }

        $message = $statusCode === 404
            ? 'Endpoint API tidak ditemukan.'
            : 'Terjadi kesalahan pada server.';

        $response->setStatusCode($statusCode)->setJSON([
            'success' => false,
            'message' => $message,
            'errors'  => [],
        ])->send();

        if (ENVIRONMENT !== 'testing') {
            exit($exitCode);
        }
    }
}

<?php

namespace App\Controllers\Api;

use App\Controllers\RestfullController;
use App\Entities\User;
use App\Models\UserModel;
use Myth\Auth\Password;

class Auth extends RestfullController
{
    public function login()
    {
        $input = $this->getJsonInput();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

        $login = trim($input['login'] ?? $input['email'] ?? $input['username'] ?? '');
        $password = (string) ($input['password'] ?? '');

        if ($login === '' || $password === '') {
            return $this->respondAuth(422, false, 'Email/username dan password wajib diisi.');
        }

        $userModel = new UserModel();
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = $userModel->where($field, $login)->first();

        if (!$user || ! Password::verify($password, $user->password_hash)) {
            return $this->respondAuth(401, false, 'Email/username atau password salah.');
        }

        if ((int) $user->active !== 1) {
            return $this->respondAuth(403, false, 'Akun belum aktif.');
        }

        return $this->respondAuth(200, true, 'Login berhasil.', $this->buildSession($user));
    }

    public function register()
    {
        $input = $this->getJsonInput();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

        $name = trim($input['name'] ?? $input['nama'] ?? '');
        $email = trim($input['email'] ?? '');
        $phone = trim($input['phone'] ?? $input['no_hp'] ?? '');
        $password = (string) ($input['password'] ?? '');
        $passwordConfirmation = (string) ($input['password_confirmation'] ?? $input['pass_confirm'] ?? '');

        if ($name === '' || $email === '' || $phone === '' || $password === '' || $passwordConfirmation === '') {
            return $this->respondAuth(422, false, 'Semua field wajib diisi.');
        }

        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->respondAuth(422, false, 'Format email tidak valid.');
        }

        if ($password !== $passwordConfirmation) {
            return $this->respondAuth(422, false, 'Konfirmasi password tidak sama.');
        }

        $userModel = new UserModel();
        $username = $this->makeUsername($email, $userModel);

        $user = new User([
            'email' => $email,
            'username' => $username,
            'password' => $password,
            'active' => 1,
        ]);

        $userModel->withGroup('user');
        $userId = $userModel->insert($user);

        if (! $userId) {
            $errors = $userModel->errors();
            return $this->respondAuth(422, false, implode(' ', $errors) ?: 'Registrasi gagal.');
        }

        $createdUser = $userModel->find($userId);

        return $this->respondAuth(201, true, 'Registrasi berhasil.', $this->buildSession($createdUser, [
            'name' => $name,
            'phone' => $phone,
        ]));
    }

    public function logout()
    {
        return $this->respondAuth(200, true, 'Logout berhasil.');
    }

    public function profile()
    {
        return $this->respondAuth(200, true, 'Profile endpoint siap. Data user tersimpan di aplikasi mobile setelah login/register.');
    }

    private function getJsonInput(): array
    {
        return $this->requestInput();
    }

    private function makeUsername(string $email, UserModel $userModel): string
    {
        $base = strtolower((string) preg_replace('/[^a-zA-Z0-9_.-]/', '', strstr($email, '@', true) ?: 'user'));
        $base = trim($base, '._-');
        $base = substr($base !== '' ? $base : 'user', 0, 24);
        $username = $base;
        $counter = 1;

        while ($userModel->where('username', $username)->first()) {
            $suffix = (string) $counter;
            $username = substr($base, 0, 30 - strlen($suffix)) . $suffix;
            $counter++;
        }

        return $username;
    }

    private function buildSession(User $user, array $extra = []): array
    {
        $authorize = service('authorization');
        $role = $authorize->inGroup('admin', $user->id) ? 'admin' : 'user';

        return [
            'token' => bin2hex(random_bytes(32)),
            'user' => [
                'id' => (int) $user->id,
                'name' => $extra['name'] ?? $user->username,
                'username' => $user->username,
                'email' => $user->email,
                'phone' => $extra['phone'] ?? '',
                'role' => $role,
            ],
        ];
    }

    private function respondAuth(int $code, bool $status, string $message, ?array $data = null)
    {
        return $this->respond([
            'success' => $status,
            'message' => $message,
            'data' => $data,
        ], $code);
    }
}

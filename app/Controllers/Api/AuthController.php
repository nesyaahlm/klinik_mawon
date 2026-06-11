<?php

namespace App\Controllers\Api;

use App\Entities\User;
use App\Models\ApiTokenModel;
use App\Models\ProfileModel;
use App\Models\UserModel;
use Myth\Auth\Password;
use Throwable;

class AuthController extends BaseApiController
{
    public function register()
    {
        $input = $this->input();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

        $name = trim((string) ($input['name'] ?? $input['nama'] ?? ''));
        $phone = trim((string) ($input['phone'] ?? $input['no_hp'] ?? ''));
        $email = trim((string) ($input['email'] ?? ''));
        $username = trim((string) ($input['username'] ?? ''));

        if ($username === '' && $email !== '') {
            $username = $this->makeUsername($email);
        }

        $rules = [
            'username' => 'required|alpha_numeric_punct|min_length[3]|max_length[30]|is_unique[users.username]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[8]',
        ];

        $validationInput = array_merge($input, [
            'username' => $username,
            'email'    => $email,
        ]);

        if (! $this->validateData($validationInput, $rules)) {
            return $this->failure('Validasi gagal.', $this->validator->getErrors(), 422);
        }

        $passwordConfirmation = $input['password_confirmation'] ?? $input['pass_confirm'] ?? null;
        if ($passwordConfirmation !== ($input['password'] ?? null)) {
            return $this->failure('Validasi gagal.', [
                'password_confirmation' => 'Konfirmasi password tidak sesuai.',
            ], 422);
        }

        $user = new User([
            'username' => $username,
            'email'    => $email,
        ]);
        $user->password = $input['password'];
        $user->activate();

        $userModel = new UserModel();
        if (! $userModel->save($user)) {
            return $this->failure('Registrasi gagal.', $userModel->errors(), 422);
        }

        $userId = (int) ($user->id ?? $userModel->getInsertID());
        $this->saveOptionalProfile($userId, array_merge($input, [
            'name'  => $name,
            'phone' => $phone,
        ]));

        $token = (new ApiTokenModel())->issueToken($userId, $input['device_name'] ?? null);
        $createdUser = $userModel->find($userId);

        return $this->success([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $this->publicUserWithProfile($createdUser),
        ], 'Registrasi berhasil.', 201);
    }

    public function login()
    {
        $input = $this->input();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

        $login = trim((string) ($input['login'] ?? $input['email'] ?? ''));
        $password = (string) ($input['password'] ?? '');

        if ($login === '' || $password === '') {
            return $this->failure('Login dan password wajib diisi.', [], 422);
        }

        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $user = (new UserModel())->where($field, $login)->first();

        if ($user === null || ! Password::verify($password, $user->password_hash)) {
            return $this->failure('Kredensial login tidak valid.', [], 401);
        }

        if (! $user->isActivated() || $user->isBanned()) {
            return $this->failure('Akun tidak dapat digunakan.', [], 403);
        }

        $token = (new ApiTokenModel())->issueToken((int) $user->id, $input['device_name'] ?? null);

        return $this->success([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => $this->publicUserWithProfile($user),
        ], 'Login berhasil.');
    }

    public function logout()
    {
        $plainToken = $this->bearerToken();

        if ($plainToken === null || ! (new ApiTokenModel())->revoke($plainToken)) {
            return $this->failure('Token autentikasi tidak valid.', [], 401);
        }

        return $this->success([], 'Logout berhasil.');
    }

    private function saveOptionalProfile(int $userId, array $input): void
    {
        $profile = array_filter([
            'name'    => $input['name'] ?? $input['nama'] ?? null,
            'phone'   => $input['phone'] ?? $input['no_hp'] ?? null,
            'address' => $input['address'] ?? $input['alamat'] ?? null,
        ], static fn ($value): bool => $value !== null && $value !== '');

        if ($profile !== []) {
            try {
                $profile['user_id'] = $userId;
                (new ProfileModel())->insert($profile);
            } catch (Throwable) {
                return;
            }
        }
    }

    private function makeUsername(string $email): string
    {
        $base = strtolower((string) preg_replace('/[^a-zA-Z0-9_.-]/', '', strstr($email, '@', true) ?: 'user'));
        $base = trim($base, '._-');
        $base = substr($base !== '' ? $base : 'user', 0, 24);
        $username = $base;
        $counter = 1;
        $userModel = new UserModel();

        while ($userModel->where('username', $username)->first() !== null) {
            $suffix = (string) $counter;
            $username = substr($base, 0, 30 - strlen($suffix)) . $suffix;
            $counter++;
        }

        return $username;
    }

    private function publicUserWithProfile(object $user): array
    {
        try {
            $profile = (new ProfileModel())->where('user_id', (int) $user->id)->first() ?? [];
        } catch (Throwable) {
            $profile = [];
        }

        return array_merge($this->publicUser($user), [
            'name'    => $profile['name'] ?? $user->username,
            'nama'    => $profile['name'] ?? $user->username,
            'phone'   => $profile['phone'] ?? '',
            'no_hp'   => $profile['phone'] ?? '',
            'address' => $profile['address'] ?? '',
            'alamat'  => $profile['address'] ?? '',
        ]);
    }
}

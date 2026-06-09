<?php

namespace App\Controllers\Api;

use App\Models\ProfileModel;
use App\Models\UserModel;
use Throwable;

class ProfileController extends BaseApiController
{
    public function show()
    {
        $userId = $this->currentUserId();
        if ($userId === null) {
            return $this->failure('Token autentikasi diperlukan.', [], 401);
        }

        $user = (new UserModel())->find($userId);

        if ($user === null) {
            return $this->failure('User tidak ditemukan.', [], 404);
        }

        try {
            $profile = (new ProfileModel())->where('user_id', $userId)->first();
        } catch (Throwable) {
            $profile = null;
        }

        return $this->success($this->formatProfile($user, $profile), 'Data profile berhasil diambil.');
    }

    public function update()
    {
        $userId = $this->currentUserId();
        if ($userId === null) {
            return $this->failure('Token autentikasi diperlukan.', [], 401);
        }

        $input = $this->input();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

        $input = $this->normalizeInput($input);

        $rules = [
            'email'   => 'permit_empty|valid_email',
            'name'    => 'permit_empty|max_length[255]',
            'phone'   => 'permit_empty|max_length[30]',
            'address' => 'permit_empty|max_length[1000]',
        ];

        if (! $this->validateData($input, $rules)) {
            return $this->failure('Validasi gagal.', $this->validator->getErrors(), 422);
        }

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if ($user === null) {
            return $this->failure('User tidak ditemukan.', [], 404);
        }

        if (isset($input['email']) && $input['email'] !== $user->email) {
            $otherUser = $userModel->where('email', $input['email'])->where('id !=', $userId)->first();
            if ($otherUser !== null) {
                return $this->failure('Validasi gagal.', ['email' => 'Email sudah digunakan.'], 422);
            }
            $userModel->update($userId, ['email' => $input['email']]);
        }

        $profileData = array_intersect_key($input, array_flip(['name', 'phone', 'address']));
        if ($profileData !== []) {
            try {
                $profileModel = new ProfileModel();
                $existing = $profileModel->where('user_id', $userId)->first();
                $profileData['user_id'] = $userId;

                if ($existing === null) {
                    $profileModel->insert($profileData);
                } else {
                    $profileModel->update($existing['id'], $profileData);
                }
            } catch (Throwable) {
                // Keep profile API usable when the legacy profiles table is damaged.
            }
        }

        return $this->show();
    }

    private function normalizeInput(array $input): array
    {
        $input['name'] = $input['name'] ?? $input['nama'] ?? $input['nama_lengkap'] ?? null;
        $input['phone'] = $input['phone'] ?? $input['no_hp'] ?? $input['telepon'] ?? null;
        $input['address'] = $input['address'] ?? $input['alamat'] ?? null;

        return $input;
    }

    private function formatProfile(object $user, ?array $profile): array
    {
        $name = $profile['name'] ?? $user->username;
        $phone = $profile['phone'] ?? '';
        $address = $profile['address'] ?? '';

        return [
            'id'           => (int) $user->id,
            'user_id'      => (int) $user->id,
            'name'         => $name,
            'nama'         => $name,
            'nama_lengkap' => $name,
            'username'     => $user->username,
            'email'        => $user->email,
            'phone'        => $phone,
            'no_hp'        => $phone,
            'telepon'      => $phone,
            'address'      => $address,
            'alamat'       => $address,
        ];
    }
}

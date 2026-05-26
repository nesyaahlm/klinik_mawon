<?php

namespace App\Controllers\Api;

use App\Models\ProfileModel;
use App\Models\UserModel;

class ProfileController extends BaseApiController
{
    public function show()
    {
        $userId = $this->currentUserId();
        $user = (new UserModel())->find($userId);

        if ($user === null) {
            return $this->failure('User tidak ditemukan.', [], 404);
        }

        $profile = (new ProfileModel())->where('user_id', $userId)->first();

        return $this->success([
            'user'    => $this->publicUser($user),
            'profile' => $profile,
        ]);
    }

    public function update()
    {
        $userId = $this->currentUserId();
        $input = $this->input();
        if (($invalidJson = $this->invalidJsonResponse()) !== null) {
            return $invalidJson;
        }

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
            $profileModel = new ProfileModel();
            $existing = $profileModel->where('user_id', $userId)->first();
            $profileData['user_id'] = $userId;

            if ($existing === null) {
                $profileModel->insert($profileData);
            } else {
                $profileModel->update($existing['id'], $profileData);
            }
        }

        return $this->show();
    }
}

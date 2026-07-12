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
            $profile = $this->loadLinkedProfile($userId);
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
            'email'         => 'permit_empty|valid_email',
            'name'          => 'permit_empty|max_length[255]',
            'phone'         => 'permit_empty|max_length[30]',
            'address'       => 'permit_empty|max_length[1000]',
            'birth_date'    => 'permit_empty|valid_date[Y-m-d]',
            'gender'        => 'permit_empty|in_list[male,female,laki-laki,perempuan,L,P]',
            'photo'         => 'permit_empty|max_length[255]',
            'profile_photo' => 'permit_empty|max_length[255]',
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

        try {
            $photoPath = $this->saveUploadedProfilePhoto($userId);
        } catch (Throwable $e) {
            return $this->failure('Upload foto profil gagal.', ['photo' => $e->getMessage()], 422);
        }
        if ($photoPath !== null) {
            $input['photo'] = $photoPath;
        }

        $profileData = array_intersect_key($input, array_flip(['name', 'phone', 'address', 'birth_date', 'gender', 'photo', 'profile_photo']));
        if ($profileData !== []) {
            if (isset($profileData['profile_photo']) && $profileData['profile_photo'] !== '') {
                $profileData['photo'] = $profileData['profile_photo'];
                unset($profileData['profile_photo']);
            }

            try {
                $this->saveLinkedProfile($userId, $profileData);
            } catch (Throwable $e) {
                return $this->failure('Profil gagal disimpan.', ['server' => $e->getMessage()], 500);
            }
        }

        return $this->show();
    }

    private function saveUploadedProfilePhoto(int $userId): ?string
    {
        $photoFile = $this->request->getFile('photo') ?? $this->request->getFile('profile_photo');
        if ($photoFile === null || ! $photoFile->isValid()) {
            return null;
        }

        $extension = strtolower($photoFile->getClientExtension() ?: $photoFile->guessExtension() ?: '');
        if (! in_array($extension, ['jpg', 'jpeg', 'png', 'webp'], true)) {
            throw new \RuntimeException('Foto profil harus berformat jpg, jpeg, png, atau webp.');
        }

        $uploadPath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'profiles';
        if (! is_dir($uploadPath) && ! mkdir($uploadPath, 0755, true) && ! is_dir($uploadPath)) {
            throw new \RuntimeException('Folder upload profil tidak dapat dibuat.');
        }

        $fileName = $photoFile->getRandomName();
        $photoFile->move($uploadPath, $fileName);
        return 'uploads/profiles/' . $fileName;
    }

    private function normalizeInput(array $input): array
    {
        $input['name'] = $input['name'] ?? $input['nama'] ?? $input['nama_lengkap'] ?? null;
        $input['phone'] = $input['phone'] ?? $input['no_hp'] ?? $input['nomor_hp'] ?? $input['no_telp'] ?? $input['telepon'] ?? null;
        $input['address'] = $input['address'] ?? $input['alamat'] ?? null;
        $input['birth_date'] = $input['birth_date'] ?? $input['tanggal_lahir'] ?? null;
        $input['gender'] = $input['gender'] ?? $input['jenis_kelamin'] ?? null;
        $input['photo'] = $input['photo'] ?? $input['profile_photo'] ?? null;

        return $input;
    }

    private function formatProfile(object $user, ?array $profile): array
    {
        $name = $profile['name'] ?? $user->username;
        $phone = $profile['phone'] ?? '';
        $address = $profile['address'] ?? '';
        $birthDate = $profile['birth_date'] ?? null;
        $gender = $profile['gender'] ?? null;
        $rawPhoto = $profile['photo'] ?? '';
        $photo = $this->normalizePhotoUrl($rawPhoto);

        return [
            'id'           => (int) ($profile['id'] ?? $user->id),
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
            'birth_date'   => $birthDate,
            'tanggal_lahir'=> $birthDate,
            'gender'       => $gender,
            'jenis_kelamin'=> $gender,
            'photo'        => $photo,
            'profile_photo'=> $photo,
            'photo_url'    => $photo,
            'image_url'    => $photo,
        ];
    }

    private function loadLinkedProfile(int $userId): ?array
    {
        if ($this->safeTableExists('profiles')) {
            return (new ProfileModel())->where('user_id', $userId)->first();
        }

        if ($this->safeTableExists('mobile_profiles')) {
            return db_connect()->table('mobile_profiles')->where('user_id', $userId)->get()->getRowArray();
        }

        if ($this->safeTableExists('pasien') && $this->safeFieldExists('user_id', 'pasien')) {
            $pasien = db_connect()->table('pasien')->where('user_id', $userId)->get()->getRowArray();
            if ($pasien === null) {
                return null;
            }

            return [
                'id' => $pasien['id'] ?? null,
                'user_id' => $pasien['user_id'] ?? null,
                'name' => $pasien['nama'] ?? '',
                'phone' => $pasien['phone'] ?? $pasien['no_hp'] ?? '',
                'address' => $pasien['alamat'] ?? '',
                'birth_date' => $pasien['birth_date'] ?? null,
                'gender' => $pasien['gender'] ?? null,
                'photo' => $pasien['photo'] ?? '',
            ];
        }

        return null;
    }

    private function saveLinkedProfile(int $userId, array $profileData): void
    {
        if ($this->safeTableExists('profiles')) {
            $profileModel = new ProfileModel();
            $existing = $profileModel->where('user_id', $userId)->first();
            $data = $this->filterExistingFields('profiles', ['user_id' => $userId, ...$profileData]);
            if ($existing === null) {
                $profileModel->insert($data);
            } else {
                $profileModel->update($existing['id'], $data);
            }
            return;
        }

        if ($this->safeTableExists('mobile_profiles')) {
            $data = $this->filterExistingFields('mobile_profiles', [
                'user_id' => $userId,
                ...$profileData,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
            $existing = db_connect()->table('mobile_profiles')->where('user_id', $userId)->get()->getRowArray();
            if ($existing === null) {
                $data['created_at'] = date('Y-m-d H:i:s');
                db_connect()->table('mobile_profiles')->insert($data);
            } else {
                db_connect()->table('mobile_profiles')->where('id', $existing['id'])->update($data);
            }
            return;
        }

        if (! $this->safeTableExists('pasien')) {
            throw new \RuntimeException('Tabel profil pasien belum tersedia.');
        }

        $data = $this->filterExistingFields('pasien', [
            'user_id' => $userId,
            'nama' => $profileData['name'] ?? null,
            'phone' => $profileData['phone'] ?? null,
            'alamat' => $profileData['address'] ?? null,
            'birth_date' => $profileData['birth_date'] ?? null,
            'gender' => $profileData['gender'] ?? null,
            'photo' => $profileData['photo'] ?? null,
        ]);
        $table = db_connect()->table('pasien');
        $existing = $this->safeFieldExists('user_id', 'pasien')
            ? $table->where('user_id', $userId)->get()->getRowArray()
            : null;

        if ($existing === null) {
            db_connect()->table('pasien')->insert($data);
        } else {
            db_connect()->table('pasien')->where('id', $existing['id'])->update($data);
        }
    }
}

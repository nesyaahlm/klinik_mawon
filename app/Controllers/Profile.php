<?php

namespace App\Controllers;

use App\Models\ProfileModel;
use Myth\Auth\Models\UserModel;

class Profile extends BaseController
{
    public function index()
    {
        $profileModel = new ProfileModel();
        $userModel    = new UserModel();
        $userId       = session('id'); // FIX

        $user    = $userModel->find($userId);
        $profile = $profileModel->where('user_id', $userId)->first();

        return view('profile/index', [
            'user'    => $user,
            'profile' => $profile
        ]);
    }

    public function edit()
    {
        $profileModel = new ProfileModel();
        $userModel    = new UserModel();
        $userId       = session('id'); // FIX

        $user    = $userModel->find($userId);
        $profile = $profileModel->where('user_id', $userId)->first();

        return view('profile/edit', [
            'user'    => $user,
            'profile' => $profile
        ]);
    }

   public function update()
{
    $userId = user_id();  // <- pakai helper Myth\Auth
    if (!$userId) {
        return redirect()->to('/login')->with('error', 'Silahkan login terlebih dahulu');
    }

    $profileModel = new \App\Models\ProfileModel();
    $userModel    = new \Myth\Auth\Models\UserModel();

    $profileData = [
        'user_id' => $userId,
        'name'    => $this->request->getPost('name'),
        'phone'   => $this->request->getPost('phone'),
        'address' => $this->request->getPost('address'),
    ];

    $existing = $profileModel->where('user_id', $userId)->first();

    if ($existing) {
        $profileModel->update($existing['id'], $profileData);
    } else {
        $profileModel->insert($profileData);
    }
    $userModel->update($userId, [
        'email' => $this->request->getPost('email')
    ]);

    return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
}

    public function password()
    {
        return view('profile/password');
    }

    public function updatePassword()
    {
        $userModel = new UserModel();
        $id = session('id'); // FIX

        $old = $this->request->getPost('old_password');
        $new = $this->request->getPost('new_password');

        $user = $userModel->find($id);

        if (!password_verify($old, $user['password_hash'])) {
            return redirect()->back()->with('error', "Password lama salah!");
        }

        $userModel->update($id, [
            'password_hash' => password_hash($new, PASSWORD_DEFAULT),
        ]);

        return redirect()->to('/profile')->with('success', 'Password berhasil diganti.');
    }
}

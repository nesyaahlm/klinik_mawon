<?php

namespace App\Controllers;

use Myth\Auth\Controllers\AuthController;

class Auth extends AuthController
{
    public function attemptLogin()
    {
        helper('auth');
        
        $auth = service('authentication');

        $login    = $this->request->getPost('login');
        $password = $this->request->getPost('password');
        $remember = (bool) $this->request->getPost('remember');

        if (!$login || !$password) {
            return redirect()->back()->with('error', 'Login gagal: form tidak lengkap.')->withInput();
        }

        $validField = config('Auth')->validFields[0];

        
        $credentials = [
            $validField => $login,
            'password'  => $password,
        ];

        if (! $auth->attempt($credentials, $remember)) {
            return redirect()->back()->withInput()->with('error', 'Login gagal: kredensial salah.');
        }

        if (in_groups('admin')) {
            return redirect()->to('/admin/dashboard');
        }

        return redirect()->to('/');
    }
}

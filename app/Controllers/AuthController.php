<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use Myth\Auth\Config\Auth as AuthConfig;
use Myth\Auth\Models\UserModel;
use Myth\Auth\Models\GroupModel;

class AuthController extends Controller
{
    protected $auth;
    protected $config;
    protected $session;

    public function __construct()
    {
        $this->session = service('session');
        $this->auth = service('authentication');
        $this->config = config('Auth');
    }

    // ============================================================
    // LOGIN
    // ============================================================

    public function login()
{
    if ($this->auth->check()) {
        $user = $this->auth->user();
        $authorize = service('authorization');

        if ($authorize->inGroup('admin', $user->id)) {
            return redirect()->to('/admin/dashboard');
        }

        if ($authorize->inGroup('user', $user->id)) {
            return redirect()->to('/layanan');
        }
    }

    return view($this->config->views['login'], ['config' => $this->config]);
}


  public function attemptLogin()
{
    $rules = [
        'login'    => 'required',
        'password' => 'required',
    ];

    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()
            ->with('errors', $this->validator->getErrors());
    }

    $login    = $this->request->getPost('login');
    $password = $this->request->getPost('password');
    $remember = (bool) $this->request->getPost('remember');
    $type     = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

    if (!$this->auth->attempt([$type => $login, 'password' => $password], $remember)) {
        return redirect()->back()->withInput()->with('error', lang('Auth.badAttempt'));
    }
    $user = $this->auth->user();
    $authorize = service('authorization');

    
    if ($authorize->inGroup('admin', $user->id)) {
        return redirect()->to('/admin/dashboard');
    }

    if ($authorize->inGroup('user', $user->id)) {
        return redirect()->to('/home');
    }

    return redirect()->to('/');
}



    

    public function logout()
    {
        if ($this->auth->check()) {
            $this->auth->logout();
        }
        return redirect()->to('/');
    }

   
    private function redirectByGroup($user)
    {
        $groupModel = model(GroupModel::class);

        
        $groups = $groupModel->getGroupsForUser($user->id);

    
        if (in_array('admin', $groups)) {
            return redirect()->to('/admin/dashboard');
        }

        if (in_array('user', $groups)) {
            return redirect()->to('/layanan');
        }

        
        return redirect()->to('/');
    }
}

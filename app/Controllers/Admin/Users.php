<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;

class Users extends BaseController
{
    protected $userModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
    }


    public function index()
    {
        $data['users'] = $this->userModel->findAll();
        return view('admin/users/index', $data);
    }

    public function create()
    {
        return view('admin/users/create');
    }

    public function store()
    {
        $data = [
            'email'    => $this->request->getPost('email'),
            'username' => $this->request->getPost('username'),
            'password_hash' => password_hash(
                $this->request->getPost('password'),
                PASSWORD_DEFAULT
            ),
            'active'   => 1,
        ];

        $this->userModel
            ->withGroup('user')   
            ->insert($data);

      
        if ($this->userModel->errors()) {
            dd($this->userModel->errors());
        }

        return redirect()->to('/admin/users')
            ->with('success', 'User berhasil ditambahkan!');
    }

   public function update($id)
{
    $old = $this->userModel->find($id);

    $username = $this->request->getPost('username');
    $email    = $this->request->getPost('email');

    $data = [
        'username' => $username !== '' ? $username : $old['username'],
        'email'    => $email !== '' ? $email : $old['email'],
    ];

    $this->userModel->update($id, $data);

    return redirect()->to('/admin/users')
        ->with('success', 'User berhasil diupdate!');
}



    public function delete($id)
    {
        $this->userModel->delete($id);
        return redirect()->to('/admin/users');
    }

    public function edit($id)
{
    $data['user'] = $this->userModel->find($id);

    if (!$data['user']) {
        return redirect()->to('/admin/users');
    }

    return view('admin/users/edit', $data);
}

}

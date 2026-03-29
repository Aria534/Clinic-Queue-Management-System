<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function index()
    {
        if (session()->get('logged_in')) {
            return $this->redirectByRole();
        }
        return view('User/auth', ['page' => 'login']);
    }

    public function register()
    {
        if (session()->get('logged_in')) {
            return $this->redirectByRole();
        }
        return view('User/auth', ['page' => 'register']);
    }

    public function login()
    {
        $rules = [
            'email'    => 'required|valid_email',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $user  = $model->findByEmail($this->request->getPost('email'));

        if (!$user || !$model->verifyPassword($this->request->getPost('password'), $user['password'])) {
            return redirect()->back()->withInput()->with('error', 'Invalid email or password.');
        }

        session()->set([
            'logged_in' => true,
            'user_id'   => $user['id'],
            'name'      => $user['name'],
            'email'     => $user['email'],
            'role'      => $user['role'],
        ]);

        return $this->redirectByRole();
    }

    public function store()
    {
        $rules = [
            'name'     => 'required|min_length[2]',
            'email'    => 'required|valid_email|is_unique[users.email]',
            'phone'    => 'required',
            'password' => 'required|min_length[6]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $model = new UserModel();
        $model->save([
            'name'     => $this->request->getPost('name'),
            'email'    => $this->request->getPost('email'),
            'phone'    => $this->request->getPost('phone'),
            'password' => password_hash($this->request->getPost('password'), PASSWORD_DEFAULT),
            'role'     => 'patient',
        ]);

        return redirect()->to(base_url('login'))->with('success', 'Registration successful! Please login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(base_url('login'))->with('success', 'Logged out successfully.');
    }

    private function redirectByRole()
    {
        return session()->get('role') === 'admin'
            ? redirect()->to(base_url('admin/dashboard'))
            : redirect()->to(base_url('patient/dashboard'));
    }
}
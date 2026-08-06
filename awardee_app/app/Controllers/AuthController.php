<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController
{
    public function login()
    {
        helper(['form', 'url']);

        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        if (strtolower($this->request->getMethod()) === 'post') {
            $rules = [
                'identity' => 'required|min_length[3]',
                'password' => 'required|min_length[6]',
            ];

            if (! $this->validate($rules)) {
                return view('auth/login', [
                    'validation' => $this->validator,
                ]);
            }

            $identity = trim((string) $this->request->getPost('identity'));
            $password = (string) $this->request->getPost('password');

            $userModel = new UserModel();
            $user = $userModel->findByUsernameOrEmail($identity);

            if ($user && password_verify($password, $user['password_hash'])) {
                $sessionData = [
                    'user_id'     => $user['id'],
                    'fullname'    => $user['fullname'],
                    'username'    => $user['username'],
                    'email'       => $user['email'],
                    'role'        => $user['role'],
                    'isLoggedIn'  => true,
                ];
            } else {
                return redirect()->back()->withInput()->with('error', 'Invalid credentials.');
            }

            session()->set($sessionData);
            session()->regenerate(true);

            return redirect()->to(site_url('dashboard'))->with('success', 'Login successful.');
        }

        return view('auth/login');
    }

    public function forgotPassword()
    {
        helper(['form']);

        if (strtolower($this->request->getMethod()) === 'post') {
            $rules = [
                'email' => 'required|valid_email',
            ];

            if (! $this->validate($rules)) {
                return view('auth/forgot_password', [
                    'validation' => $this->validator,
                ]);
            }

            // Placeholder for token/email workflow in next auth enhancement phase.
            return redirect()->to(site_url('login'))->with('success', 'If the email exists, password reset instructions were initiated.');
        }

        return view('auth/forgot_password');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to(site_url('login'))->with('success', 'You have been logged out.');
    }

}

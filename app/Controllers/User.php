<?php

namespace App\Controllers;

use App\Models\UserModel;

class User extends BaseController
{
    public function login()
    {
        $title = 'Login';

        if ($this->request->getMethod() === 'post') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');

            // Debug: Log login attempt
            log_message('info', 'Login attempt for username: ' . $username);

            $userModel = new UserModel();
            $user = $userModel->where('username', $username)->first();

            if ($user) {
                log_message('info', 'User found: ' . $user['username']);

                if (password_verify($password, $user['userpassword'])) {
                    // Login berhasil
                    log_message('info', 'Password verified for user: ' . $username);

                    session()->set([
                        'user_id' => $user['id'],
                        'username' => $user['username'],
                        'useremail' => $user['useremail'],
                        'logged_in' => true
                    ]);

                    log_message('info', 'Session set for user: ' . $username);
                    log_message('info', 'Redirecting to admin panel');

                    session()->setFlashdata('success', 'Login berhasil!');
                    return redirect()->to('/admin/artikel');
                } else {
                    log_message('info', 'Password verification failed for user: ' . $username);
                    session()->setFlashdata('error', 'Password salah!');
                }
            } else {
                log_message('info', 'User not found: ' . $username);
                session()->setFlashdata('error', 'Username tidak ditemukan!');
            }
        }

        return view('auth/login', compact('title'));
    }
    
    public function register()
    {
        $title = 'Register';
        
        if ($this->request->getMethod() === 'post') {
            $userModel = new UserModel();
            
            $data = [
                'username' => $this->request->getPost('username'),
                'useremail' => $this->request->getPost('useremail'),
                'userpassword' => password_hash($this->request->getPost('userpassword'), PASSWORD_DEFAULT)
            ];
            
            if ($userModel->insert($data)) {
                session()->setFlashdata('success', 'Registrasi berhasil! Silakan login.');
                return redirect()->to('/user/login');
            } else {
                session()->setFlashdata('error', 'Registrasi gagal!');
            }
        }
        
        return view('auth/register', compact('title'));
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/user/login');
    }
}

<?php

namespace App\Controllers;

use App\Models\UserModel;

class Auth extends BaseController
{
    public function login()
    {
        $title = 'Login';
        
        if ($this->request->getMethod() === 'post') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            
            $userModel = new UserModel();
            $user = $userModel->where('username', $username)->first();
            
            if ($user && password_verify($password, $user['userpassword'])) {
                // Login berhasil
                session()->set([
                    'user_id' => $user['id'],
                    'username' => $user['username'],
                    'useremail' => $user['useremail'],
                    'logged_in' => true
                ]);
                
                session()->setFlashdata('success', 'Login berhasil!');
                return redirect()->to('/admin/artikel');
            } else {
                // Login gagal
                session()->setFlashdata('error', 'Username atau password salah!');
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
                return redirect()->to('/auth/login');
            } else {
                session()->setFlashdata('error', 'Registrasi gagal!');
            }
        }
        
        return view('auth/register', compact('title'));
    }
    
    public function logout()
    {
        session()->destroy();
        session()->setFlashdata('success', 'Logout berhasil!');
        return redirect()->to('/auth/login');
    }

}

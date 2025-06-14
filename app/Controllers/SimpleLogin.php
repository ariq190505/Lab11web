<?php

namespace App\Controllers;

class SimpleLogin extends BaseController
{
    public function index()
    {
        $title = 'Simple Login';
        
        if ($this->request->getMethod() === 'post') {
            $username = $this->request->getPost('username');
            $password = $this->request->getPost('password');
            
            // Simple hardcoded login (untuk testing)
            if ($username === 'admin' && $password === 'admin123') {
                // Login berhasil
                session()->set([
                    'user_id' => 1,
                    'username' => 'admin',
                    'useremail' => 'admin@lab7web.com',
                    'logged_in' => true
                ]);
                
                session()->setFlashdata('success', 'Login berhasil!');
                return redirect()->to('/admin/artikel');
            } else {
                // Login gagal
                session()->setFlashdata('error', 'Username atau password salah!');
            }
        }
        
        return view('auth/simple_login', compact('title'));
    }
    
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/simplelogin');
    }
}

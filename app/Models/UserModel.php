<?php

namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'user';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $allowedFields = ['username', 'useremail', 'userpassword'];
    
    // Timestamps
    protected $useTimestamps = false;
    
    // Validation rules
    protected $validationRules = [
        'username' => 'required|min_length[3]|max_length[200]|is_unique[user.username]',
        'useremail' => 'required|valid_email|is_unique[user.useremail]',
        'userpassword' => 'required|min_length[6]'
    ];
    
    protected $validationMessages = [
        'username' => [
            'required' => 'Username harus diisi',
            'min_length' => 'Username minimal 3 karakter',
            'is_unique' => 'Username sudah digunakan'
        ],
        'useremail' => [
            'required' => 'Email harus diisi',
            'valid_email' => 'Format email tidak valid',
            'is_unique' => 'Email sudah terdaftar'
        ],
        'userpassword' => [
            'required' => 'Password harus diisi',
            'min_length' => 'Password minimal 6 karakter'
        ]
    ];
}

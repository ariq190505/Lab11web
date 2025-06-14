<?php

namespace App\Controllers;

use App\Models\UserModel;

class SessionDebug extends BaseController
{
    public function test()
    {
        echo "<h2>Session Debug Test</h2>";
        
        // Test 1: Session ID
        echo "<h3>1. Session Info:</h3>";
        echo "Session ID: " . session_id() . "<br>";
        echo "Session Status: " . (session_status() === PHP_SESSION_ACTIVE ? 'Active' : 'Inactive') . "<br>";
        
        // Test 2: Set test session
        session()->set('test_key', 'test_value');
        echo "<h3>2. Set Test Session:</h3>";
        echo "Set 'test_key' = 'test_value'<br>";
        
        // Test 3: Get test session
        echo "<h3>3. Get Test Session:</h3>";
        echo "Get 'test_key' = " . session()->get('test_key') . "<br>";
        
        // Test 4: Current login session
        echo "<h3>4. Current Login Session:</h3>";
        echo "logged_in: " . (session()->get('logged_in') ? 'true' : 'false') . "<br>";
        echo "username: " . (session()->get('username') ?? 'not set') . "<br>";
        echo "user_id: " . (session()->get('user_id') ?? 'not set') . "<br>";
        
        // Test 5: Manual login test
        echo "<h3>5. Manual Login Test:</h3>";
        $userModel = new UserModel();
        $user = $userModel->where('username', 'admin')->first();
        
        if ($user) {
            echo "User found: " . $user['username'] . "<br>";
            
            // Set session manually
            session()->set([
                'user_id' => $user['id'],
                'username' => $user['username'],
                'useremail' => $user['useremail'],
                'logged_in' => true
            ]);
            
            echo "Session set manually<br>";
            echo "Now logged_in: " . (session()->get('logged_in') ? 'true' : 'false') . "<br>";
            echo "Now username: " . session()->get('username') . "<br>";
            
            echo "<br><a href='" . base_url('/admin/artikel') . "'>Test Admin Access</a><br>";
            echo "<a href='" . base_url('/user/login') . "'>Back to Login</a>";
        } else {
            echo "User admin not found!<br>";
        }
    }
    
    public function clear()
    {
        session()->destroy();
        echo "Session cleared!<br>";
        echo "<a href='" . base_url('/sessiondebug/test') . "'>Test Again</a>";
    }

    public function forceLogin()
    {
        // Force login tanpa database
        session()->set([
            'user_id' => 1,
            'username' => 'admin',
            'useremail' => 'admin@lab7web.com',
            'logged_in' => true
        ]);

        echo "<h2>Force Login Berhasil!</h2>";
        echo "<p>Session telah di-set secara manual</p>";
        echo "<p>Username: " . session()->get('username') . "</p>";
        echo "<p>Logged in: " . (session()->get('logged_in') ? 'Yes' : 'No') . "</p>";
        echo "<br><a href='" . base_url('/admin/artikel') . "'>Test Admin Panel</a>";
    }
}

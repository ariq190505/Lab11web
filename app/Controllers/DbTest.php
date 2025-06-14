<?php

namespace App\Controllers;

class DbTest extends BaseController
{
    public function test()
    {
        echo "<h2>Database Connection Test</h2>";
        
        try {
            $db = \Config\Database::connect();
            
            if ($db->connID) {
                echo "<p style='color: green;'>✅ Database connection successful!</p>";
                
                // Test query
                $query = $db->query("SHOW TABLES");
                $tables = $query->getResultArray();
                
                echo "<h3>Tables in database:</h3>";
                echo "<ul>";
                foreach ($tables as $table) {
                    $tableName = array_values($table)[0];
                    echo "<li>$tableName</li>";
                }
                echo "</ul>";
                
                // Test user table
                if ($db->tableExists('user')) {
                    $userQuery = $db->query("SELECT COUNT(*) as count FROM user");
                    $userCount = $userQuery->getRow()->count;
                    echo "<p>User table exists with $userCount records</p>";
                    
                    if ($userCount > 0) {
                        $users = $db->query("SELECT username FROM user")->getResultArray();
                        echo "<p>Users: ";
                        foreach ($users as $user) {
                            echo $user['username'] . " ";
                        }
                        echo "</p>";
                    }
                } else {
                    echo "<p style='color: red;'>❌ User table does not exist!</p>";
                }
                
            } else {
                echo "<p style='color: red;'>❌ Database connection failed!</p>";
            }
            
        } catch (\Exception $e) {
            echo "<p style='color: red;'>❌ Database error: " . $e->getMessage() . "</p>";
        }
        
        echo "<br><a href='" . base_url('/sessiondebug/forceLogin') . "'>Force Login (Bypass DB)</a>";
    }
}

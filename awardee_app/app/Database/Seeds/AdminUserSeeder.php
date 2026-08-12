<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        // Get admin credentials from environment variables
        // Set these in .env before running the seeder
        $adminUsername = getenv('ADMIN_USERNAME') ?: 'awardeeadmin';
        $adminEmail    = getenv('ADMIN_EMAIL') ?: 'admin@awardee.local';
        $adminPassword = getenv('ADMIN_PASSWORD');

        if (empty($adminPassword) || strlen($adminPassword) < 8) {
            echo "ERROR: ADMIN_PASSWORD environment variable must be set to at least 8 characters.\n";
            echo "Example: ADMIN_PASSWORD='YourStrongPassword123!'\n";
            return;
        }

        $data = [
            'fullname'      => 'Awardee System Administrator',
            'username'      => $adminUsername,
            'email'         => $adminEmail,
            'password_hash' => password_hash($adminPassword, PASSWORD_DEFAULT),
            'role'          => 'admin',
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->table('users')->where('username', $adminUsername)->get()->getRowArray();

        if (! $existing) {
            $this->db->table('users')->insert($data);
            echo "✓ Admin user '{$adminUsername}' created successfully.\n";
        } else {
            $this->db->table('users')
                ->where('username', $adminUsername)
                ->update([
                    'fullname'      => $data['fullname'],
                    'email'         => $data['email'],
                    'password_hash' => $data['password_hash'],
                    'role'          => $data['role'],
                    'status'        => 'active',
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
            echo "✓ Admin user '{$adminUsername}' updated successfully.\n";
        }
    }
}
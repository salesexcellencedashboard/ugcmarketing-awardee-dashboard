<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminUserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'fullname'      => 'Awardee System Admin',
            'username'      => 'awardeeadmin',
            'email'         => 'awardee.admin@gmail.com',
            'password_hash' => password_hash('12345678', PASSWORD_DEFAULT),
            'role'          => 'admin',
            'status'        => 'active',
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ];

        $existing = $this->db->table('users')->where('username', 'awardeeadmin')->get()->getRowArray();

        if (! $existing) {
            $this->db->table('users')->insert($data);
        } else {
            $this->db->table('users')
                ->where('username', 'awardeeadmin')
                ->update([
                    'fullname'      => $data['fullname'],
                    'email'         => $data['email'],
                    'password_hash' => $data['password_hash'],
                    'role'          => $data['role'],
                    'status'        => 'active',
                    'updated_at'    => date('Y-m-d H:i:s'),
                ]);
        }
    }
}
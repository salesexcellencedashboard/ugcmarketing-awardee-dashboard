<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Seed authentication data
        $this->call('AdminUserSeeder');
        // Seed reference data (regions) - needed for system structure
        $this->call('InitialReferenceSeeder');
        // Product sales seeder - intentionally empty, data imported via CSV/UI
        $this->call('ProductSalesSeeder');
    }
}
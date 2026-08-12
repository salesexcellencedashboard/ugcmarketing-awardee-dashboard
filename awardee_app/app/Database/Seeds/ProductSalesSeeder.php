<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSalesSeeder extends Seeder
{
    public function run()
    {
        // Product sales data is imported via CSV/UI in the Data Entry module.
        // This seeder is intentionally empty to prevent sample data from
        // being inserted into production.
        echo "✓ Product sales seeder skipped - data is imported via CSV/UI.\n";
    }
}
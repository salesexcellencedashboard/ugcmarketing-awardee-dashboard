<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ProductSalesSeeder extends Seeder
{
    public function run()
    {
        $data = [
            // Dealer 1 - North Prime Steel
            ['dealer_id' => 1, 'sales_month' => 1, 'sales_year' => 2026, 'prime_bended_sheets' => 75, 'prime_spandrel_sheets' => 65, 'steel_deck_sheets' => 80, 'cpurlins_sheets' => 50, 'total_sheets' => 270, 'uploaded_by' => 1],
            ['dealer_id' => 1, 'sales_month' => 2, 'sales_year' => 2026, 'prime_bended_sheets' => 82, 'prime_spandrel_sheets' => 68, 'steel_deck_sheets' => 85, 'cpurlins_sheets' => 55, 'total_sheets' => 290, 'uploaded_by' => 1],
            ['dealer_id' => 1, 'sales_month' => 3, 'sales_year' => 2026, 'prime_bended_sheets' => 88, 'prime_spandrel_sheets' => 72, 'steel_deck_sheets' => 90, 'cpurlins_sheets' => 60, 'total_sheets' => 310, 'uploaded_by' => 1],
            
            // Dealer 2 - North Build Depot
            ['dealer_id' => 2, 'sales_month' => 1, 'sales_year' => 2026, 'prime_bended_sheets' => 60, 'prime_spandrel_sheets' => 45, 'steel_deck_sheets' => 70, 'cpurlins_sheets' => 40, 'total_sheets' => 215, 'uploaded_by' => 1],
            ['dealer_id' => 2, 'sales_month' => 2, 'sales_year' => 2026, 'prime_bended_sheets' => 65, 'prime_spandrel_sheets' => 50, 'steel_deck_sheets' => 75, 'cpurlins_sheets' => 45, 'total_sheets' => 235, 'uploaded_by' => 1],
            ['dealer_id' => 2, 'sales_month' => 3, 'sales_year' => 2026, 'prime_bended_sheets' => 72, 'prime_spandrel_sheets' => 55, 'steel_deck_sheets' => 82, 'cpurlins_sheets' => 50, 'total_sheets' => 259, 'uploaded_by' => 1],
            
            // Dealer 3 - South Roofing Center
            ['dealer_id' => 3, 'sales_month' => 1, 'sales_year' => 2026, 'prime_bended_sheets' => 80, 'prime_spandrel_sheets' => 70, 'steel_deck_sheets' => 85, 'cpurlins_sheets' => 60, 'total_sheets' => 295, 'uploaded_by' => 1],
            ['dealer_id' => 3, 'sales_month' => 2, 'sales_year' => 2026, 'prime_bended_sheets' => 85, 'prime_spandrel_sheets' => 75, 'steel_deck_sheets' => 90, 'cpurlins_sheets' => 65, 'total_sheets' => 315, 'uploaded_by' => 1],
            ['dealer_id' => 3, 'sales_month' => 3, 'sales_year' => 2026, 'prime_bended_sheets' => 92, 'prime_spandrel_sheets' => 78, 'steel_deck_sheets' => 95, 'cpurlins_sheets' => 70, 'total_sheets' => 335, 'uploaded_by' => 1],
            
            // Dealer 4 - South Metal Traders
            ['dealer_id' => 4, 'sales_month' => 1, 'sales_year' => 2026, 'prime_bended_sheets' => 70, 'prime_spandrel_sheets' => 55, 'steel_deck_sheets' => 75, 'cpurlins_sheets' => 50, 'total_sheets' => 250, 'uploaded_by' => 1],
            ['dealer_id' => 4, 'sales_month' => 2, 'sales_year' => 2026, 'prime_bended_sheets' => 75, 'prime_spandrel_sheets' => 60, 'steel_deck_sheets' => 80, 'cpurlins_sheets' => 55, 'total_sheets' => 270, 'uploaded_by' => 1],
            ['dealer_id' => 4, 'sales_month' => 3, 'sales_year' => 2026, 'prime_bended_sheets' => 82, 'prime_spandrel_sheets' => 65, 'steel_deck_sheets' => 88, 'cpurlins_sheets' => 60, 'total_sheets' => 295, 'uploaded_by' => 1],
        ];

        // Using Query Builder
        $this->db->table('product_sales')->insertBatch($data);
    }
}

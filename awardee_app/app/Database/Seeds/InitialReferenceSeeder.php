<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class InitialReferenceSeeder extends Seeder
{
    public function run()
    {
        $now = date('Y-m-d H:i:s');

        // ==========================================
        // 4 REGIONS - matching Excel format exactly
        // ==========================================
        $regionNames = [
            'SOUTH LUZON',
            'NORTH & CENTRAL LUZON',
            'VISAYAS',
            'MINDANAO',
        ];

        $regionIds = [];
        foreach ($regionNames as $name) {
            $exists = $this->db->table('regions')
                ->where('region_name', $name)
                ->get()
                ->getRowArray();
            if (!$exists) {
                $this->db->table('regions')->insert([
                    'region_name' => $name,
                    'description' => $name . ' operational area',
                    'created_at'  => $now,
                ]);
                $regionIds[$name] = $this->db->insertID();
            } else {
                $regionIds[$name] = $exists['id'];
            }
        }

        echo "✓ Seeded: " . count($regionNames) . " regions\n";
        echo "  Note: Stores and dealers are added via the Data Entry module.\n";
    }
}
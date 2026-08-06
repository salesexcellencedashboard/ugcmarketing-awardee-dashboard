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

        // ==========================================
        // STORES (2 per region)
        // ==========================================
        $storesByRegion = [
            'SOUTH LUZON' => [
                ['store_name' => 'South Luzon Main', 'address' => 'Calamba, Laguna'],
                ['store_name' => 'South Luzon Satellite', 'address' => 'Dasmarinas, Cavite'],
            ],
            'NORTH & CENTRAL LUZON' => [
                ['store_name' => 'NCL Main', 'address' => 'San Fernando, Pampanga'],
                ['store_name' => 'NCL Satellite', 'address' => 'Malolos, Bulacan'],
            ],
            'VISAYAS' => [
                ['store_name' => 'Visayas Main', 'address' => 'Cebu City, Cebu'],
                ['store_name' => 'Visayas Satellite', 'address' => 'Iloilo City, Iloilo'],
            ],
            'MINDANAO' => [
                ['store_name' => 'Mindanao Main', 'address' => 'Davao City, Davao'],
                ['store_name' => 'Mindanao Satellite', 'address' => 'Cagayan de Oro City'],
            ],
        ];

        $storeIds = [];
        foreach ($storesByRegion as $region => $stores) {
            $rid = $regionIds[$region];
            foreach ($stores as $s) {
                $exists = $this->db->table('stores')
                    ->where('store_name', $s['store_name'])
                    ->get()
                    ->getRowArray();
                if (!$exists) {
                    $this->db->table('stores')->insert([
                        'store_name' => $s['store_name'],
                        'region_id'  => $rid,
                        'address'    => $s['address'],
                        'status'     => 'active',
                        'created_at' => $now,
                    ]);
                    $storeIds[$s['store_name']] = $this->db->insertID();
                } else {
                    $storeIds[$s['store_name']] = $exists['id'];
                }
            }
        }

        // ==========================================
        // DEALERS (3 per region)
        // ==========================================
        $dealersByRegion = [
            'SOUTH LUZON' => [
                ['dealer_code' => 'D-SL-001', 'dealer_name' => 'South Builders Inc.', 'contact' => '09170000001', 'email' => 'south.builders@ugc.local'],
                ['dealer_code' => 'D-SL-002', 'dealer_name' => 'Laguna Construction Supply', 'contact' => '09170000002', 'email' => 'laguna.cs@ugc.local'],
                ['dealer_code' => 'D-SL-003', 'dealer_name' => 'Cavite Metal Traders', 'contact' => '09170000003', 'email' => 'cavite.metal@ugc.local'],
            ],
            'NORTH & CENTRAL LUZON' => [
                ['dealer_code' => 'D-NC-001', 'dealer_name' => 'North Builders Inc.', 'contact' => '09170000004', 'email' => 'north.builders@ugc.local'],
                ['dealer_code' => 'D-NC-002', 'dealer_name' => 'Pampanga Construction Supply', 'contact' => '09170000005', 'email' => 'pampanga.cs@ugc.local'],
                ['dealer_code' => 'D-NC-003', 'dealer_name' => 'Bulacan Metal Traders', 'contact' => '09170000006', 'email' => 'bulacan.metal@ugc.local'],
            ],
            'VISAYAS' => [
                ['dealer_code' => 'D-VS-001', 'dealer_name' => 'Visayas Builders Inc.', 'contact' => '09170000007', 'email' => 'visayas.builders@ugc.local'],
                ['dealer_code' => 'D-VS-002', 'dealer_name' => 'Cebu Construction Supply', 'contact' => '09170000008', 'email' => 'cebu.cs@ugc.local'],
                ['dealer_code' => 'D-VS-003', 'dealer_name' => 'Iloilo Metal Traders', 'contact' => '09170000009', 'email' => 'iloilo.metal@ugc.local'],
            ],
            'MINDANAO' => [
                ['dealer_code' => 'D-MN-001', 'dealer_name' => 'Mindanao Builders Inc.', 'contact' => '09170000010', 'email' => 'mindanao.builders@ugc.local'],
                ['dealer_code' => 'D-MN-002', 'dealer_name' => 'Davao Construction Supply', 'contact' => '09170000011', 'email' => 'davao.cs@ugc.local'],
                ['dealer_code' => 'D-MN-003', 'dealer_name' => 'CDO Metal Traders', 'contact' => '09170000012', 'email' => 'cdo.metal@ugc.local'],
            ],
        ];

        $dealerIds = [];
        foreach ($dealersByRegion as $region => $dealers) {
            $rid = $regionIds[$region];
            $regionStores = array_keys($storesByRegion[$region]);
            foreach ($dealers as $i => $d) {
                $storeName = $regionStores[$i % count($regionStores)];
                $sid = $storeIds[$storesByRegion[$region][$storeName === $regionStores[0] ? 0 : 1]['store_name']] ?? 1;

                $exists = $this->db->table('dealers')
                    ->where('dealer_code', $d['dealer_code'])
                    ->get()
                    ->getRowArray();
                if (!$exists) {
                    $this->db->table('dealers')->insert([
                        'dealer_code' => $d['dealer_code'],
                        'dealer_name' => $d['dealer_name'],
                        'store_id'    => $sid,
                        'region_id'   => $rid,
                        'contact_no'  => $d['contact'],
                        'email'       => $d['email'],
                        'created_at'  => $now,
                    ]);
                    $dealerIds[$d['dealer_name']] = $this->db->insertID();
                } else {
                    $dealerIds[$d['dealer_name']] = $exists['id'];
                }
            }
        }

        echo "✓ Seeded: " . count($regionNames) . " regions, " . count($storeIds) . " stores, " . count($dealerIds) . " dealers\n";
    }
}
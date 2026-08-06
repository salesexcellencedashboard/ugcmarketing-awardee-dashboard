<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUUIDToDataEntryTables extends Migration
{
    public function up()
    {
        // Add uuid column to sales_excellence_data
        $this->forge->addColumn('sales_excellence_data', [
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'after' => 'id',
                'unique' => true,
            ],
        ]);

        // Add uuid column to top_branch_data
        $this->forge->addColumn('top_branch_data', [
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'after' => 'id',
                'unique' => true,
            ],
        ]);

        // Add uuid column to elite_circle_summary
        $this->forge->addColumn('elite_circle_summary', [
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'after' => 'id',
                'unique' => true,
            ],
        ]);

        // Add uuid column to elite_circle_records
        $this->forge->addColumn('elite_circle_records', [
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'after' => 'id',
                'unique' => true,
            ],
        ]);

        // Backfill existing records with UUIDs
        $tables = [
            'sales_excellence_data',
            'top_branch_data',
            'elite_circle_summary',
            'elite_circle_records',
        ];

        $db = \Config\Database::connect();
        foreach ($tables as $table) {
            $records = $db->table($table)
                ->where('uuid IS NULL')
                ->get()
                ->getResultArray();
            
            foreach ($records as $record) {
                $uuid = $this->generateUUID();
                $db->table($table)
                    ->where('id', $record['id'])
                    ->update(['uuid' => $uuid]);
            }
        }
    }

    public function down()
    {
        $this->forge->dropColumn('sales_excellence_data', 'uuid');
        $this->forge->dropColumn('top_branch_data', 'uuid');
        $this->forge->dropColumn('elite_circle_summary', 'uuid');
        $this->forge->dropColumn('elite_circle_records', 'uuid');
    }

    private function generateUUID(): string
    {
        $data = random_bytes(16);
        // Set version to 4
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set variant to RFC 4122
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
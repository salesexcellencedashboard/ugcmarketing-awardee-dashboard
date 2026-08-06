<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoColumnToDataEntryTables extends Migration
{
    public function up()
    {
        // Add photo column to sales_excellence_data
        $fields = [
            'photo' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => null,
                'after' => 'growth',
            ],
        ];
        
        try {
            if (!$this->db->fieldExists('photo', 'sales_excellence_data')) {
                $this->forge->addColumn('sales_excellence_data', $fields);
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration - sales_excellence_data photo column: ' . $e->getMessage());
        }

        // Add photo column to top_branch_data
        try {
            if (!$this->db->fieldExists('photo', 'top_branch_data')) {
                $this->forge->addColumn('top_branch_data', [
                    'photo' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255,
                        'null' => true,
                        'default' => null,
                        'after' => 'revenue',
                    ],
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration - top_branch_data photo column: ' . $e->getMessage());
        }

        // Add photo column to elite_circle_data
        try {
            if (!$this->db->fieldExists('photo', 'elite_circle_data')) {
                $this->forge->addColumn('elite_circle_data', [
                    'photo' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255,
                        'null' => true,
                        'default' => null,
                        'after' => 'revenue',
                    ],
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration - elite_circle_data photo column: ' . $e->getMessage());
        }

        // Add photo column to elite_circle_summary (used by the Data Entry EC tab)
        try {
            if (!$this->db->fieldExists('photo', 'elite_circle_summary')) {
                $this->forge->addColumn('elite_circle_summary', [
                    'photo' => [
                        'type' => 'VARCHAR',
                        'constraint' => 255,
                        'null' => true,
                        'default' => null,
                        'after' => 'total_cm',
                    ],
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration - elite_circle_summary photo column: ' . $e->getMessage());
        }
    }

    public function down()
    {
        try {
            if ($this->db->fieldExists('photo', 'sales_excellence_data')) {
                $this->forge->dropColumn('sales_excellence_data', 'photo');
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration down - sales_excellence_data: ' . $e->getMessage());
        }

        try {
            if ($this->db->fieldExists('photo', 'top_branch_data')) {
                $this->forge->dropColumn('top_branch_data', 'photo');
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration down - top_branch_data: ' . $e->getMessage());
        }

        try {
            if ($this->db->fieldExists('photo', 'elite_circle_data')) {
                $this->forge->dropColumn('elite_circle_data', 'photo');
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration down - elite_circle_data: ' . $e->getMessage());
        }

        try {
            if ($this->db->fieldExists('photo', 'elite_circle_summary')) {
                $this->forge->dropColumn('elite_circle_summary', 'photo');
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration down - elite_circle_summary: ' . $e->getMessage());
        }
    }
}
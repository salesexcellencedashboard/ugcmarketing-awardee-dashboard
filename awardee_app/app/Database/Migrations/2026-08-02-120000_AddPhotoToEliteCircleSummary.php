<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoToEliteCircleSummary extends Migration
{
    public function up()
    {
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
            if ($this->db->fieldExists('photo', 'elite_circle_summary')) {
                $this->forge->dropColumn('elite_circle_summary', 'photo');
            }
        } catch (\Exception $e) {
            log_message('error', 'Migration down - elite_circle_summary: ' . $e->getMessage());
        }
    }
}
<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEliteCircleRecords extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'region' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => false,
            ],
            'area' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'position' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'category' => [
                'type' => 'ENUM',
                'constraint' => ['volume', 'cm'],
                'default' => 'volume',
                'null' => false,
            ],
            'sales_volume' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'sales_cm' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('region');
        $this->forge->addKey('category');
        $this->forge->createTable('elite_circle_records', true);
    }

    public function down()
    {
        $this->forge->dropTable('elite_circle_records', true);
    }
}
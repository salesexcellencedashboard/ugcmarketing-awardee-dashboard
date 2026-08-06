<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDataEntryTables extends Migration
{
    public function up()
    {
        // Sales Excellence Awardee Data table
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
                'constraint' => ['attainment', 'margin'],
                'default' => 'attainment',
                'null' => false,
            ],
            'attainment_percent' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'actual_volume' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'budget' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'revenue' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'actual_cm' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'price_lf' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'margin' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'growth' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
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
        $this->forge->createTable('sales_excellence_data', true);

        // Top Branch Recognition Data table
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
            'sales_office' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
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
                'constraint' => ['growth', 'attainment'],
                'default' => 'growth',
                'null' => false,
            ],
            'growth_percent' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'attainment_percent' => [
                'type' => 'DECIMAL',
                'constraint' => '10,2',
                'default' => 0.00,
            ],
            'actual' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'budget' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'last_month' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'current_month' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'revenue' => [
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
        $this->forge->createTable('top_branch_data', true);

        // Elite Circle Summary table (generated quarterly)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'quarter_year' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'comment' => 'e.g. Q1-2026',
            ],
            'region' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'top_volume_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'top_volume_area' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'top_volume_position' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'top_volume_value' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'top_cm_name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'top_cm_area' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'top_cm_position' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => true,
            ],
            'top_cm_value' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'total_volume' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'total_cm' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'generated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('quarter_year');
        $this->forge->addKey('region');
        $this->forge->createTable('elite_circle_summary', true);
    }

    public function down()
    {
        $this->forge->dropTable('sales_excellence_data', true);
        $this->forge->dropTable('top_branch_data', true);
        $this->forge->dropTable('elite_circle_summary', true);
    }
}
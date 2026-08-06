<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSteelDeckTable extends Migration
{
    public function up()
    {
        // Steel Deck Data table (same as top_branch_data but without category)
        $this->forge->addField([
            'id' => [
                'type' => 'INT',
                'constraint' => 11,
                'unsigned' => true,
                'auto_increment' => true,
            ],
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 36,
                'null' => true,
                'unique' => true,
            ],
            'region' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'company' => [
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
            'quantity_invoice' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'gross_amount' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'volume' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'revenue' => [
                'type' => 'DECIMAL',
                'constraint' => '15,2',
                'default' => 0.00,
            ],
            'sales_month' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 1,
            ],
            'sales_year' => [
                'type' => 'SMALLINT',
                'constraint' => 4,
                'default' => 2026,
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
        $this->forge->addKey('uuid');
        $this->forge->createTable('steel_deck_data', true);
    }

    public function down()
    {
        $this->forge->dropTable('steel_deck_data', true);
    }
}
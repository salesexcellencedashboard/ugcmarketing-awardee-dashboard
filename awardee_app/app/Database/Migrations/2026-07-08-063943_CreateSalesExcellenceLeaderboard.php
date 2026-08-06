<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalesExcellenceLeaderboard extends Migration
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
            'uuid' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
                'unique' => true,
            ],
            'name' => [
                'type' => 'VARCHAR',
                'constraint' => 200,
                'null' => false,
            ],
            'region' => [
                'type' => 'VARCHAR',
                'constraint' => 100,
                'null' => false,
            ],
            'area' => [
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
            'jan_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'feb_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'mar_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'apr_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'may_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'jun_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'jul_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'aug_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'sep_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'oct_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'nov_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'dec_rank' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'total_top' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'default' => 0,
                'null' => false,
            ],
            'sales_year' => [
                'type' => 'SMALLINT',
                'constraint' => 4,
                'null' => false,
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
        $this->forge->addKey('name');
        $this->forge->addKey('sales_year');
        $this->forge->createTable('sales_excellence_leaderboard', true);
    }

    public function down()
    {
        $this->forge->dropTable('sales_excellence_leaderboard', true);
    }
}

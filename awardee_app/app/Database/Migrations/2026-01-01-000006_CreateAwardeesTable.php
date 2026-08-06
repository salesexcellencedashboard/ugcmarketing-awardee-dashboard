<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAwardeesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'BIGINT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'dealer_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'sales_month' => [
                'type'       => 'TINYINT',
                'unsigned'   => true,
                'constraint' => 2,
            ],
            'sales_year' => [
                'type'       => 'SMALLINT',
                'unsigned'   => true,
                'constraint' => 4,
            ],
            'ranking' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'award_title' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['sales_month', 'sales_year']);
        $this->forge->addKey('dealer_id');
        $this->forge->addUniqueKey(['dealer_id', 'sales_month', 'sales_year', 'award_title'], 'uq_awardees_dealer_month_year_title');
        $this->forge->addForeignKey('dealer_id', 'dealers', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('awardees', true);
    }

    public function down()
    {
        $this->forge->dropTable('awardees', true);
    }
}

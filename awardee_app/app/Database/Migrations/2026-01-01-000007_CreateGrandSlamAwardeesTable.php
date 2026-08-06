<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateGrandSlamAwardeesTable extends Migration
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
            'total_awards' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
            ],
            'qualification_status' => [
                'type'       => 'ENUM',
                'constraint' => ['qualified', 'not_qualified'],
                'default'    => 'not_qualified',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('dealer_id');
        $this->forge->addForeignKey('dealer_id', 'dealers', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('grand_slam_awardees', true);
    }

    public function down()
    {
        $this->forge->dropTable('grand_slam_awardees', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateDealersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'dealer_code' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'dealer_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'store_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'region_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'contact_no' => [
                'type'       => 'VARCHAR',
                'constraint' => 30,
                'null'       => true,
            ],
            'email' => [
                'type'       => 'VARCHAR',
                'constraint' => 120,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('dealer_code');
        $this->forge->addKey('store_id');
        $this->forge->addKey('region_id');
        $this->forge->addForeignKey('store_id', 'stores', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('region_id', 'regions', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('dealers', true);
    }

    public function down()
    {
        $this->forge->dropTable('dealers', true);
    }
}

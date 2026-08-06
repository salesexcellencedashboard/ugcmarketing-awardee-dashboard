<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateStoresTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'store_name' => [
                'type'       => 'VARCHAR',
                'constraint' => 150,
            ],
            'region_id' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'address' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'status' => [
                'type'       => 'ENUM',
                'constraint' => ['active', 'inactive'],
                'default'    => 'active',
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('region_id');
        $this->forge->addForeignKey('region_id', 'regions', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('stores', true);
    }

    public function down()
    {
        $this->forge->dropTable('stores', true);
    }
}

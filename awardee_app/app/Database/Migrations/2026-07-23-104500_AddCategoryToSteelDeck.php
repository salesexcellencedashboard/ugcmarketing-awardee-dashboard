<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddCategoryToSteelDeck extends Migration
{
    public function up()
    {
        $this->forge->addColumn('steel_deck_data', [
            'category' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'default' => 'volume',
                'null' => false,
                'after' => 'position',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('steel_deck_data', 'category');
    }
}
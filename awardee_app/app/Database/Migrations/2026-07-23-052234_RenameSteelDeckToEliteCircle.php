<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameSteelDeckToEliteCircle extends Migration
{
    public function up()
    {
        // Rename table from steel_deck_data to elite_circle_data
        $this->forge->renameTable('steel_deck_data', 'elite_circle_data');
    }

    public function down()
    {
        $this->forge->renameTable('elite_circle_data', 'steel_deck_data');
    }
}
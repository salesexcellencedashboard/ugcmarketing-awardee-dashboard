<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMarginToTopBranchCategory extends Migration
{
    public function up()
    {
        $this->db->query("ALTER TABLE top_branch_data MODIFY COLUMN category ENUM('growth','attainment','margin') DEFAULT 'growth' NOT NULL");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE top_branch_data MODIFY COLUMN category ENUM('growth','attainment') DEFAULT 'growth' NOT NULL");
    }
}
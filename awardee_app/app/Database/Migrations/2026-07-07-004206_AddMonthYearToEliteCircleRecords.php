<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMonthYearToEliteCircleRecords extends Migration
{
    public function up()
    {
        $this->forge->addColumn('elite_circle_records', [
            'sales_month' => [
                'type' => 'TINYINT',
                'constraint' => 2,
                'null' => false,
                'default' => 1,
                'after' => 'category',
            ],
            'sales_year' => [
                'type' => 'SMALLINT',
                'constraint' => 4,
                'null' => false,
                'default' => 2026,
                'after' => 'sales_month',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('elite_circle_records', 'sales_month');
        $this->forge->dropColumn('elite_circle_records', 'sales_year');
    }
}
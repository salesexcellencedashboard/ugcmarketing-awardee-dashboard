<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMonthYearToDataEntryTables extends Migration
{
    public function up()
    {
        // Add sales_month and sales_year to sales_excellence_data
        $this->forge->addColumn('sales_excellence_data', [
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

        // Add sales_month and sales_year to top_branch_data
        $this->forge->addColumn('top_branch_data', [
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
        $this->forge->dropColumn('sales_excellence_data', 'sales_month');
        $this->forge->dropColumn('sales_excellence_data', 'sales_year');
        $this->forge->dropColumn('top_branch_data', 'sales_month');
        $this->forge->dropColumn('top_branch_data', 'sales_year');
    }
}
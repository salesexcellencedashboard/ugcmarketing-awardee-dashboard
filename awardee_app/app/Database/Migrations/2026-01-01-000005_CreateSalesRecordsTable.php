<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSalesRecordsTable extends Migration
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
            'sales_amount' => [
                'type'       => 'DECIMAL',
                'constraint' => '15,2',
                'default'    => 0.00,
            ],
            'units_sold' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
            ],
            'uploaded_by' => [
                'type'     => 'INT',
                'unsigned' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['dealer_id', 'sales_month', 'sales_year']);
        $this->forge->addKey('uploaded_by');
        $this->forge->addUniqueKey(['dealer_id', 'sales_month', 'sales_year'], 'uq_sales_dealer_month_year');
        $this->forge->addForeignKey('dealer_id', 'dealers', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('sales_records', true);
    }

    public function down()
    {
        $this->forge->dropTable('sales_records', true);
    }
}

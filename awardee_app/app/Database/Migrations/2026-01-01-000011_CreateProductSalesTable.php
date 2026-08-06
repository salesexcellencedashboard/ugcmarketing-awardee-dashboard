<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProductSalesTable extends Migration
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
            'prime_bended_sheets' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
            ],
            'prime_spandrel_sheets' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
            ],
            'steel_deck_sheets' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
            ],
            'cpurlins_sheets' => [
                'type'     => 'INT',
                'unsigned' => true,
                'default'  => 0,
            ],
            'total_sheets' => [
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
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['dealer_id', 'sales_month', 'sales_year']);
        $this->forge->addKey('uploaded_by');
        $this->forge->addKey('sales_year');
        $this->forge->addKey('sales_month');
        $this->forge->addUniqueKey(['dealer_id', 'sales_month', 'sales_year'], 'uq_product_sales_dealer_month_year');
        $this->forge->addForeignKey('dealer_id', 'dealers', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('uploaded_by', 'users', 'id', 'CASCADE', 'RESTRICT');
        $this->forge->createTable('product_sales', true);
    }

    public function down()
    {
        $this->forge->dropTable('product_sales', true);
    }
}

<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class UpdateSalesRecordsForProducts extends Migration
{
    public function up()
    {
        // Add new columns to sales_records table
        $this->forge->addColumn('sales_records', [
            'product_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Product category: Prime Bended, Prime Sprandrel, Steel Deck, C-Purlins',
            ],
            'volume_sheets' => [
                'type'    => 'INT',
                'null'    => true,
                'comment' => 'Volume in sheets',
            ],
            'region' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
                'comment'    => 'Geographic region: ENCL, South Luzon, Visayas, Mindanao',
            ],
        ]);
    }

    public function down()
    {
        // Drop columns if migration is rolled back
        $this->forge->dropColumn('sales_records', ['product_type', 'volume_sheets', 'region']);
    }
}

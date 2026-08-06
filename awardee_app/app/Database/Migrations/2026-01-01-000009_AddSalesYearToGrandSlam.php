<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSalesYearToGrandSlam extends Migration
{
    public function up()
    {
        // Check if sales_year column exists first
        $fields = $this->db->getFieldData('grand_slam_awardees');
        $hasSalesYear = false;
        foreach ($fields as $field) {
            if ($field->name === 'sales_year') {
                $hasSalesYear = true;
                break;
            }
        }

        if (!$hasSalesYear) {
            $this->forge->addColumn('grand_slam_awardees', [
                'sales_year' => [
                    'type'     => 'SMALLINT',
                    'unsigned' => true,
                    'default'  => 2026,
                    'after'    => 'dealer_id',
                ],
            ]);
        }

        // Drop old unique key on dealer_id only, add new composite unique
        try {
            $this->db->query('ALTER TABLE grand_slam_awardees DROP INDEX uq_grand_slam_dealer_id');
        } catch (\Exception $e) {
            // Index might not exist
        }
        
        try {
            $this->db->query('ALTER TABLE grand_slam_awardees ADD UNIQUE KEY uq_grand_slam_dealer_year (`dealer_id`, `sales_year`)');
        } catch (\Exception $e) {
            // Might already exist - ignore
        }
    }

    public function down()
    {
        try {
            $this->db->query('ALTER TABLE grand_slam_awardees DROP COLUMN sales_year');
        } catch (\Exception $e) {}
        try {
            $this->db->query('ALTER TABLE grand_slam_awardees DROP INDEX uq_grand_slam_dealer_year');
        } catch (\Exception $e) {}
        try {
            $this->db->query('ALTER TABLE grand_slam_awardees ADD UNIQUE KEY uq_grand_slam_dealer_id (`dealer_id`)');
        } catch (\Exception $e) {}
    }
}
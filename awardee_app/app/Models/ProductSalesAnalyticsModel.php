<?php

namespace App\Models;

use CodeIgniter\Model;

class ProductSalesAnalyticsModel extends Model
{
    protected $table = 'product_sales';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'dealer_id',
        'sales_month',
        'sales_year',
        'prime_bended_sheets',
        'prime_spandrel_sheets',
        'steel_deck_sheets',
        'cpurlins_sheets',
        'total_sheets',
        'uploaded_by',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all sales data for a specific dealer and month
     */
    public function getDealerMonthlyData(int $dealerId, int $year, int $month): ?array
    {
        return $this->select('ps.*, d.dealer_code, d.dealer_name, r.region_name')
            ->from('product_sales ps')
            ->join('dealers d', 'd.id = ps.dealer_id', 'left')
            ->join('regions r', 'r.id = d.region_id', 'left')
            ->where('ps.dealer_id', $dealerId)
            ->where('ps.sales_year', $year)
            ->where('ps.sales_month', $month)
            ->first();
    }

    /**
     * Get all sales data for a specific month across all dealers
     * Includes dealer info from joined tables
     */
    public function getMonthlySnapshot(int $year, int $month): array
    {
        return $this->select('ps.*, d.dealer_code, d.dealer_name, r.region_name')
            ->from('product_sales ps')
            ->join('dealers d', 'd.id = ps.dealer_id', 'left')
            ->join('regions r', 'r.id = d.region_id', 'left')
            ->where('ps.sales_year', $year)
            ->where('ps.sales_month', $month)
            ->orderBy('r.region_name', 'ASC')
            ->orderBy('d.dealer_name', 'ASC')
            ->findAll();
    }

    /**
     * Get regional performance for a month
     */
    public function getRegionalData(string $region, int $year, int $month): array
    {
        return $this->select('ps.*, d.dealer_code, d.dealer_name, r.region_name')
            ->from('product_sales ps')
            ->join('dealers d', 'd.id = ps.dealer_id', 'left')
            ->join('regions r', 'r.id = d.region_id', 'left')
            ->where('r.region_name', $region)
            ->where('ps.sales_year', $year)
            ->where('ps.sales_month', $month)
            ->orderBy('d.dealer_name', 'ASC')
            ->findAll();
    }

    /**
     * Get yearly trend for a specific dealer
     */
    public function getDealerYearlyTrend(int $dealerId, int $year): array
    {
        return $this->where('dealer_id', $dealerId)
            ->where('sales_year', $year)
            ->orderBy('sales_month', 'ASC')
            ->findAll();
    }

    /**
     * Get top performers for a specific month
     */
    public function getTopPerformersByProduct(string $product, int $year, int $month, int $limit = 10): array
    {
        $column = match($product) {
            'prime_bended' => 'prime_bended_sheets',
            'prime_spandrel' => 'prime_spandrel_sheets',
            'steel_deck' => 'steel_deck_sheets',
            'cpurlins' => 'cpurlins_sheets',
            default => 'prime_bended_sheets'
        };

        return $this->select('ps.*, d.dealer_code, d.dealer_name')
            ->from('product_sales ps')
            ->join('dealers d', 'd.id = ps.dealer_id', 'left')
            ->where('ps.sales_year', $year)
            ->where('ps.sales_month', $month)
            ->orderBy($column, 'DESC')
            ->limit($limit)
            ->findAll();
    }

    /**
     * Get summary stats for a month
     */
    public function getMonthlySummaryStats(int $year, int $month): array
    {
        $data = $this->select([
            'SUM(prime_bended_sheets) as total_bended',
            'SUM(prime_spandrel_sheets) as total_spandrel',
            'SUM(steel_deck_sheets) as total_deck',
            'SUM(cpurlins_sheets) as total_purlins',
            'COUNT(DISTINCT dealer_id) as total_dealers',
        ])
            ->where('sales_year', $year)
            ->where('sales_month', $month)
            ->first();

        return $data ?? [];
    }

    /**
     * Get product-specific trends across months
     */
    public function getProductTrend(string $product, int $year): array
    {
        $column = match($product) {
            'prime_bended' => 'prime_bended_sheets',
            'prime_spandrel' => 'prime_spandrel_sheets',
            'steel_deck' => 'steel_deck_sheets',
            'cpurlins' => 'cpurlins_sheets',
            default => 'prime_bended_sheets'
        };

        return $this->select("sales_month, SUM({$column}) as total")
            ->where('sales_year', $year)
            ->groupBy('sales_month')
            ->orderBy('sales_month', 'ASC')
            ->findAll();
    }

    /**
     * Load CSV data into database
     */
    public function loadCsvData(array $rows, int $uploadedById): array
    {
        $result = ['inserted' => 0, 'updated' => 0, 'errors' => []];
        $this->db->transBegin();

        try {
            foreach ($rows as $index => $row) {
                if (empty($row['dealer_id']) || empty($row['sales_month']) || empty($row['sales_year'])) {
                    continue;
                }

                $data = [
                    'dealer_id' => $row['dealer_id'],
                    'sales_month' => $row['sales_month'],
                    'sales_year' => $row['sales_year'],
                    'prime_bended_sheets' => $row['prime_bended_sheets'] ?? 0,
                    'prime_spandrel_sheets' => $row['prime_spandrel_sheets'] ?? 0,
                    'steel_deck_sheets' => $row['steel_deck_sheets'] ?? 0,
                    'cpurlins_sheets' => $row['cpurlins_sheets'] ?? 0,
                    'total_sheets' => ($row['prime_bended_sheets'] ?? 0) + ($row['prime_spandrel_sheets'] ?? 0) + 
                                     ($row['steel_deck_sheets'] ?? 0) + ($row['cpurlins_sheets'] ?? 0),
                    'uploaded_by' => $uploadedById,
                ];

                // Try to update first, then insert if not found
                $existing = $this->where('dealer_id', $data['dealer_id'])
                    ->where('sales_month', $data['sales_month'])
                    ->where('sales_year', $data['sales_year'])
                    ->first();

                if ($existing) {
                    $this->update($existing['id'], $data);
                    $result['updated']++;
                } else {
                    $this->insert($data);
                    $result['inserted']++;
                }
            }

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                $result['errors'][] = 'Database transaction failed';
                return $result;
            }

            $this->db->transCommit();
        } catch (\Exception $e) {
            $this->db->transRollback();
            $result['errors'][] = $e->getMessage();
        }

        return $result;
    }
}

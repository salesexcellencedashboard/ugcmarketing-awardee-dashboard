<?php

namespace App\Models;

use CodeIgniter\Model;
use Config\Database;

class DashboardAnalyticsModel extends Model
{
    protected $db;

    public function __construct()
    {
        parent::__construct();
        $this->db = Database::connect();
    }

    /**
     * Apply common filters to a query builder.
     */
    private function applyFilters($builder, array $filters): void
    {
        if (!empty($filters['year'])) {
            $builder->where('sr.sales_year', (int) $filters['year']);
        }
        if (!empty($filters['month'])) {
            $builder->where('sr.sales_month', (int) $filters['month']);
        }
        if (!empty($filters['region'])) {
            $builder->where('r.region_name', $filters['region']);
        }
        if (!empty($filters['dealer_name'])) {
            $builder->where('d.dealer_name', $filters['dealer_name']);
        }
        if (!empty($filters['award_status'])) {
            if ($filters['award_status'] === 'Qualified') {
                $builder->where("EXISTS (SELECT 1 FROM awardees aw WHERE aw.dealer_id = d.id AND aw.sales_year = sr.sales_year AND aw.sales_month = sr.sales_month)");
            } elseif ($filters['award_status'] === 'Not Qualified') {
                $builder->where("NOT EXISTS (SELECT 1 FROM awardees aw WHERE aw.dealer_id = d.id AND aw.sales_year = sr.sales_year AND aw.sales_month = sr.sales_month)");
            }
        }
        if (!empty($filters['grand_slam_status'])) {
            if ($filters['grand_slam_status'] === 'Yes') {
                $builder->where("EXISTS (SELECT 1 FROM grand_slam_awardees gs WHERE gs.dealer_id = d.id AND gs.qualification_status = 'qualified')");
            } elseif ($filters['grand_slam_status'] === 'No') {
                $builder->where("NOT EXISTS (SELECT 1 FROM grand_slam_awardees gs WHERE gs.dealer_id = d.id AND gs.qualification_status = 'qualified')");
            }
        }
    }

    /**
     * Apply filters to awardees table queries.
     */
    private function applyAwardeeFilters($builder, array $filters): void
    {
        if (!empty($filters['year'])) {
            $builder->where('a.sales_year', (int) $filters['year']);
        }
        if (!empty($filters['month'])) {
            $builder->where('a.sales_month', (int) $filters['month']);
        }
        if (!empty($filters['region'])) {
            $builder->where('r.region_name', $filters['region']);
        }
        if (!empty($filters['dealer_name'])) {
            $builder->where('d.dealer_name', $filters['dealer_name']);
        }
    }

    public function getKpiSummary(array $filters = []): array
    {
        $totalDealers = (int) $this->db->table('dealers')->countAllResults();
        $totalStores = (int) $this->db->table('stores')->countAllResults();
        $totalRegions = (int) $this->db->table('regions')->countAllResults();

        $year = !empty($filters['year']) ? (int) $filters['year'] : date('Y');
        $month = !empty($filters['month']) ? (int) $filters['month'] : date('n');

        $salesBuilder = $this->db->table('sales_records sr')
            ->selectSum('sr.sales_amount')
            ->join('dealers d', 'd.id = sr.dealer_id', 'LEFT')
            ->join('regions r', 'r.id = d.region_id', 'LEFT')
            ->where('sr.sales_year', $year)
            ->where('sr.sales_month', $month);
        $this->applyFilters($salesBuilder, $filters);
        $monthlySalesRow = $salesBuilder->get()->getRowArray();

        $awardeeBuilder = $this->db->table('awardees a')
            ->join('dealers d', 'd.id = a.dealer_id', 'LEFT')
            ->join('regions r', 'r.id = d.region_id', 'LEFT')
            ->where('a.sales_year', $year)
            ->where('a.sales_month', $month);
        $this->applyAwardeeFilters($awardeeBuilder, $filters);
        $topAwardees = (int) $awardeeBuilder->countAllResults();

        $grandSlam = (int) $this->db->table('grand_slam_awardees')
            ->where('qualification_status', 'qualified')
            ->countAllResults();

        return [
            'totalDealers' => $totalDealers,
            'totalStores' => $totalStores,
            'totalRegions' => $totalRegions,
            'monthlySales' => (float) ($monthlySalesRow['sales_amount'] ?? 0),
            'topAwardees' => $topAwardees,
            'grandSlam' => $grandSlam,
        ];
    }

    public function getMonthlySalesTrend(array $filters = []): array
    {
        $year = !empty($filters['year']) ? (int) $filters['year'] : date('Y');

        $builder = $this->db->table('sales_records sr')
            ->select('sr.sales_month, SUM(sr.sales_amount) AS total_sales')
            ->join('dealers d', 'd.id = sr.dealer_id', 'LEFT')
            ->join('regions r', 'r.id = d.region_id', 'LEFT')
            ->where('sr.sales_year', $year)
            ->groupBy('sr.sales_month')
            ->orderBy('sr.sales_month', 'ASC');

        if (!empty($filters['region'])) {
            $builder->where('r.region_name', $filters['region']);
        }
        if (!empty($filters['dealer_name'])) {
            $builder->where('d.dealer_name', $filters['dealer_name']);
        }
        if (!empty($filters['award_status'])) {
            if ($filters['award_status'] === 'Qualified') {
                $builder->where("EXISTS (SELECT 1 FROM awardees aw WHERE aw.dealer_id = d.id AND aw.sales_year = sr.sales_year AND aw.sales_month = sr.sales_month)");
            } elseif ($filters['award_status'] === 'Not Qualified') {
                $builder->where("NOT EXISTS (SELECT 1 FROM awardees aw WHERE aw.dealer_id = d.id AND aw.sales_year = sr.sales_year AND aw.sales_month = sr.sales_month)");
            }
        }
        if (!empty($filters['grand_slam_status'])) {
            if ($filters['grand_slam_status'] === 'Yes') {
                $builder->where("EXISTS (SELECT 1 FROM grand_slam_awardees gs WHERE gs.dealer_id = d.id AND gs.qualification_status = 'qualified')");
            } elseif ($filters['grand_slam_status'] === 'No') {
                $builder->where("NOT EXISTS (SELECT 1 FROM grand_slam_awardees gs WHERE gs.dealer_id = d.id AND gs.qualification_status = 'qualified')");
            }
        }

        $rows = $builder->get()->getResultArray();

        $map = array_fill(1, 12, 0.0);
        foreach ($rows as $row) {
            $month = (int) $row['sales_month'];
            $map[$month] = (float) $row['total_sales'];
        }

        return [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            'data' => array_values($map),
        ];
    }

    public function getRegionalPerformance(array $filters = []): array
    {
        $year = !empty($filters['year']) ? (int) $filters['year'] : date('Y');
        $month = !empty($filters['month']) ? (int) $filters['month'] : date('n');

        $builder = $this->db->table('sales_records sr')
            ->join('dealers d', 'd.id = sr.dealer_id', 'LEFT')
            ->join('regions r', 'r.id = d.region_id', 'LEFT')
            ->select('r.region_name, SUM(sr.sales_amount) AS total_sales, SUM(sr.units_sold) AS total_volume')
            ->where('sr.sales_year', $year)
            ->where('sr.sales_month', $month)
            ->groupBy('r.region_name')
            ->orderBy('total_sales', 'DESC');

        $this->applyFilters($builder, $filters);

        $rows = $builder->get()->getResultArray();

        return [
            'labels' => array_column($rows, 'region_name'),
            'dataSales' => array_map(static fn ($r) => (float) ($r['total_sales'] ?? 0), $rows),
            'dataVolume' => array_map(static fn ($r) => (int) ($r['total_volume'] ?? 0), $rows),
        ];
    }

    public function getTopDealers(array $filters = [], int $limit = 10): array
    {
        $year = !empty($filters['year']) ? (int) $filters['year'] : date('Y');
        $month = !empty($filters['month']) ? (int) $filters['month'] : date('n');

        $builder = $this->db->table('sales_records sr')
            ->join('dealers d', 'd.id = sr.dealer_id', 'LEFT')
            ->join('regions r', 'r.id = d.region_id', 'LEFT')
            ->select('d.dealer_code, d.dealer_name, SUM(sr.sales_amount) AS total_sales, SUM(sr.units_sold) AS total_volume')
            ->where('sr.sales_year', $year)
            ->where('sr.sales_month', $month)
            ->groupBy('d.dealer_code, d.dealer_name')
            ->orderBy('total_sales', 'DESC')
            ->limit($limit);

        $this->applyFilters($builder, $filters);

        $rows = $builder->get()->getResultArray();

        return array_map(function ($p, $rank) {
            return [
                'rank' => $rank + 1,
                'dealer_code' => $p['dealer_code'] ?? '-',
                'dealer_name' => $p['dealer_name'] ?? '-',
                'sales' => (float) ($p['total_sales'] ?? 0),
                'volume' => (int) ($p['total_volume'] ?? 0),
            ];
        }, $rows, array_keys($rows));
    }

    public function getAwardDistribution(array $filters = []): array
    {
        $year = !empty($filters['year']) ? (int) $filters['year'] : date('Y');
        $month = !empty($filters['month']) ? (int) $filters['month'] : date('n');

        $builder = $this->db->table('awardees a')
            ->select('a.award_title, COUNT(*) AS count')
            ->join('dealers d', 'd.id = a.dealer_id', 'LEFT')
            ->join('regions r', 'r.id = d.region_id', 'LEFT')
            ->where('a.sales_year', $year)
            ->where('a.sales_month', $month)
            ->groupBy('a.award_title')
            ->orderBy('count', 'DESC');

        $this->applyAwardeeFilters($builder, $filters);

        $awardCounts = $builder->get()->getResultArray();

        return [
            'labels' => array_column($awardCounts, 'award_title'),
            'data' => array_map(static fn ($a) => (int) $a['count'], $awardCounts),
        ];
    }

    public function getSmartInsights(array $filters = []): array
    {
        $year = !empty($filters['year']) ? (int) $filters['year'] : date('Y');
        $month = !empty($filters['month']) ? (int) $filters['month'] : date('n');

        $builder = $this->db->table('sales_records sr')
            ->selectSum('sr.sales_amount')
            ->selectSum('sr.units_sold')
            ->join('dealers d', 'd.id = sr.dealer_id', 'LEFT')
            ->join('regions r', 'r.id = d.region_id', 'LEFT')
            ->where('sr.sales_year', $year)
            ->where('sr.sales_month', $month);

        $this->applyFilters($builder, $filters);

        $currentRow = $builder->get()->getRowArray();

        $currentSales = (float) ($currentRow['sales_amount'] ?? 0);
        $currentVolume = (int) ($currentRow['units_sold'] ?? 0);

        return [
            'currentSales' => $currentSales,
            'currentVolume' => $currentVolume,
            'growthRate' => 0.0,
            'narrative' => $currentSales > 0 ? "Sales data for $year-$month: ₱" . number_format($currentSales, 2) : 'No summary yet.',
            'recommendations' => [],
        ];
    }

    public function getTopAwardees(array $filters = []): array
    {
        $year = !empty($filters['year']) ? (int) $filters['year'] : date('Y');
        $month = !empty($filters['month']) ? (int) $filters['month'] : date('n');

        $builder = $this->db->table('awardees a')
            ->join('dealers d', 'd.id = a.dealer_id', 'LEFT')
            ->join('regions r', 'r.id = d.region_id', 'LEFT')
            ->select('d.dealer_name, r.region_name, a.award_title, a.ranking')
            ->where('a.sales_year', $year)
            ->where('a.sales_month', $month)
            ->orderBy('a.ranking', 'ASC')
            ->limit(3);

        $this->applyAwardeeFilters($builder, $filters);

        $awardees = $builder->get()->getResultArray();

        return array_map(function ($a) {
            return [
                'dealer' => $a['dealer_name'] ?? '-',
                'region' => $a['region_name'] ?? '-',
                'award' => $a['award_title'] ?? 'N/A',
                'rank' => (int) ($a['ranking'] ?? 0),
            ];
        }, $awardees);
    }

    public function getAwardeeHistory(array $filters = []): array
    {
        $year = !empty($filters['year']) ? (int) $filters['year'] : date('Y');
        $month = !empty($filters['month']) ? (int) $filters['month'] : date('n');

        $builder = $this->db->table('awardees a')
            ->join('dealers d', 'd.id = a.dealer_id', 'LEFT')
            ->join('stores s', 's.id = d.store_id', 'LEFT')
            ->join('regions r', 'r.id = d.region_id', 'LEFT')
            ->select('d.dealer_name, s.store_name, r.region_name, a.award_title')
            ->where('a.sales_year', $year)
            ->where('a.sales_month', $month)
            ->orderBy('a.ranking', 'ASC');

        $this->applyAwardeeFilters($builder, $filters);

        $awardees = $builder->get()->getResultArray();

        return array_map(function ($a) {
            return [
                'dealer' => $a['dealer_name'] ?? '-',
                'store' => $a['store_name'] ?? '-',
                'region' => $a['region_name'] ?? '-',
                'status' => $a['award_title'] ?? 'Participant',
            ];
        }, $awardees);
    }

    /**
     * Get all dashboard data in one call.
     */
    public function getAllData(array $filters = []): array
    {
        return [
            'kpiSummary' => $this->getKpiSummary($filters),
            'monthlySalesTrend' => $this->getMonthlySalesTrend($filters),
            'regionalPerformance' => $this->getRegionalPerformance($filters),
            'topDealers' => $this->getTopDealers($filters, 10),
            'awardDistribution' => $this->getAwardDistribution($filters),
            'smartInsights' => $this->getSmartInsights($filters),
            'topAwardees' => $this->getTopAwardees($filters),
            'awardeeHistory' => $this->getAwardeeHistory($filters),
        ];
    }
}
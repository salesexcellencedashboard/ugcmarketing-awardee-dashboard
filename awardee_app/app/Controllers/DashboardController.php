<?php

namespace App\Controllers;

use App\Models\DashboardAnalyticsModel;
use Config\Database;

class DashboardController extends BaseController
{
    protected DashboardAnalyticsModel $analyticsModel;

    public function __construct()
    {
        $this->analyticsModel = new DashboardAnalyticsModel();
    }

    public function index()
    {
        $filters = $this->extractFilters();

        // Read tab from query string: masterData, primeBended, primeSpandrel, steelDeck
        $tab = $this->request->getGet('tab') ?? 'primeBended';
        if (!in_array($tab, ['primeBended', 'primeSpandrel', 'steelDeck', 'masterData'])) {
            $tab = 'primeBended';
        }

        // Wrap KPI summary in try-catch to prevent crash if legacy tables don't exist
        try {
            $stats = $this->analyticsModel->getKpiSummary($filters);
        } catch (\Exception $e) {
            log_message('error', 'Dashboard KPI summary error: ' . $e->getMessage());
            $stats = [
                'totalDealers' => 0,
                'totalStores' => 0,
                'totalRegions' => 0,
                'monthlySales' => 0.0,
                'topAwardees' => 0,
                'grandSlam' => 0,
            ];
        }

$data = [
    'pageTitle'      => 'Awardee Performance Management Dashboard - AWARDEE System',
            'user'           => [
                'fullname' => session()->get('fullname'),
                'role'     => session()->get('role'),
            ],
            'selectedYear'   => $filters['year'],
            'selectedMonth'  => $filters['month'],
            'availableYears' => $this->getYearRange(),
            'stats'          => $stats,
            'activeTab'      => $tab,
        ];

        return view('dashboard/index', $data);
    }

    /**
     * New unified endpoint: returns all dashboard data in one JSON response.
     */
    public function allData()
    {
        $filters = $this->extractFilters();
        $data = $this->analyticsModel->getAllData($filters);
        return $this->response->setJSON($data);
    }

    // --- Individual endpoints (kept for backward compatibility) ---

    public function kpiSummary()
    {
        $filters = $this->extractFilters();
        return $this->response->setJSON($this->analyticsModel->getKpiSummary($filters));
    }

    public function monthlySalesTrend()
    {
        $filters = $this->extractFilters();
        return $this->response->setJSON($this->analyticsModel->getMonthlySalesTrend($filters));
    }

    public function regionalPerformance()
    {
        $filters = $this->extractFilters();
        return $this->response->setJSON($this->analyticsModel->getRegionalPerformance($filters));
    }

    public function topDealers()
    {
        $filters = $this->extractFilters();
        return $this->response->setJSON($this->analyticsModel->getTopDealers($filters, 10));
    }

    public function awardDistribution()
    {
        $filters = $this->extractFilters();
        return $this->response->setJSON($this->analyticsModel->getAwardDistribution($filters));
    }

    public function smartInsights()
    {
        $filters = $this->extractFilters();
        return $this->response->setJSON($this->analyticsModel->getSmartInsights($filters));
    }

    public function regionalData()
    {
        $filters = $this->extractFilters();
        $region = $this->request->getGet('region') ?? 'South Luzon';
        
        // Use the model which now handles filters
        $data = $this->analyticsModel->getTopDealers($filters, 10);
        
        return $this->response->setJSON([
            'region' => $region,
            'data' => $data ?: [],
        ]);
    }

    public function topAwardees()
    {
        $filters = $this->extractFilters();
        $awardees = $this->analyticsModel->getTopAwardees($filters);
        return $this->response->setJSON([
            'labels' => array_column($awardees, 'dealer'),
            'data' => $awardees,
        ]);
    }

    public function awardeeHistory()
    {
        $filters = $this->extractFilters();
        $history = $this->analyticsModel->getAwardeeHistory($filters);
        return $this->response->setJSON([
            'labels' => array_column($history, 'dealer'),
            'data' => $history,
        ]);
    }

    // --- Filter population endpoints ---

    public function filterRegions()
    {
        $db = Database::connect();
        $regions = $db->table('regions')
            ->select('region_name')
            ->orderBy('region_name', 'ASC')
            ->get()
            ->getResultArray();
        $names = array_map(static fn($r) => $r['region_name'], $regions);
        return $this->response->setJSON($names);
    }

    public function filterDealers()
    {
        $db = Database::connect();
        $dealers = $db->table('dealers')
            ->select('dealer_name')
            ->orderBy('dealer_name', 'ASC')
            ->get()
            ->getResultArray();
        $names = array_map(static fn($d) => $d['dealer_name'], $dealers);
        return $this->response->setJSON($names);
    }

    public function filterAwardStatuses()
    {
        return $this->response->setJSON(['All', 'Qualified', 'Not Qualified']);
    }

    public function filterGrandSlamStatuses()
    {
        return $this->response->setJSON(['All', 'Yes', 'No']);
    }


    private function extractFilters(): array
    {
        return [
            'year'           => (int) ($this->request->getGet('year') ?? date('Y')),
            'month'          => (int) ($this->request->getGet('month') ?? date('n')),
            'region'         => $this->request->getGet('region') ?? '',
            'dealer_name'    => $this->request->getGet('dealer_name') ?? '',
            'award_status'   => $this->request->getGet('award_status') ?? '',
            'grand_slam_status' => $this->request->getGet('grand_slam_status') ?? '',
        ];
    }

    public function currentDataset()
    {
        try {
            $db = Database::connect();
            $session = $db->table('analytics_sessions')
                ->orderBy('id', 'DESC')
                ->limit(1)
                ->get()
                ->getRowArray();

            if (!$session) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'No dataset found.',
                    'data' => [],
                ]);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $session,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'currentDataset error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dataset not available.',
                'data' => [],
            ]);
        }
    }

    public function deleteDataset()
    {
        try {
            $db = Database::connect();
            
            // Delete all analytics sessions
            $db->table('analytics_sessions')->truncate();
            
            // Also clear sales records and awardees for clean state
            $db->table('sales_records')->truncate();
            $db->table('awardees')->truncate();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All dataset records deleted successfully.',
            ]);
        } catch (\Exception $e) {
            log_message('error', 'deleteDataset error: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Delete operation failed.',
            ]);
        }
    }

    private function getYearRange(): array
    {
        return [2025, 2026];
    }
}
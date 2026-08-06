<?php

namespace App\Controllers;

use Config\Database;

class ExecutiveController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index()
    {
        // Fetch user profile_pic from database
        $userId = session()->get('user_id');
        $profilePic = null;
        if ($userId) {
            $user = $this->db->table('users')
                ->select('profile_pic')
                ->where('id', $userId)
                ->get()
                ->getRowArray();
            $profilePic = $user['profile_pic'] ?? null;
        }

        $data = [
            'pageTitle' => 'Executive Analytics Dashboard | AWARDEE System',
            'user' => [
                'fullname'    => session()->get('fullname'),
                'role'        => session()->get('role'),
                'profile_pic' => $profilePic,
            ],
        ];
        return view('dashboard/analytics', $data);
    }

    public function availableMonths()
    {
        $year = $this->request->getGet('year') ?? date('Y');
        $months = $this->detectAvailableMonths($year);
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'available_months' => $months,
                'default_month' => count($months) > 0 ? max($months) : (int)date('n'),
                'current_month' => (int)date('n'),
            ],
        ]);
    }

    /**
     * Master API: Returns ALL executive dashboard data in one call
     */
    public function allData()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');

        // Auto-detect month if not specified
        if ($month === 'auto' || $month === '0') {
            $months = $this->detectAvailableMonths($year);
            $month = count($months) > 0 ? max($months) : (int)date('n');
        }

        $dashboard = $this->request->getGet('dashboard') ?? 'all';
        $category = $this->request->getGet('category') ?? 'all';
        $region = $this->request->getGet('region') ?? 'all';
        $search = $this->request->getGet('search') ?? '';

        $data = $this->fetchAllData($month, $year, $dashboard, $category, $region, $search);

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
        ]);
    }

    /**
     * KPI Summary
     */
    public function kpiSummary()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $data = $this->fetchKpiData($month, $year);
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * Performance Trend (monthly line chart data)
     */
    public function performanceTrend()
    {
        $year = $this->request->getGet('year') ?? date('Y');
        $data = $this->fetchPerformanceTrend($year);
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * Dashboard Comparison (Radar chart data)
     */
    public function dashboardComparison()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $data = $this->fetchDashboardComparison($month, $year);
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * Regional Performance
     */
    public function regionalPerformance()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $data = $this->fetchRegionalPerformance($month, $year);
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * Top 10 Performers
     */
    public function topPerformers()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $data = $this->fetchTopPerformers($month, $year);
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * Achievement Distribution
     */
    public function achievementDistribution()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $data = $this->fetchAchievementDistribution($month, $year);
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * Recent Achievements
     */
    public function recentAchievements()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $data = $this->fetchRecentAchievements($month, $year);
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * Employee Search (filtered by dashboard + category)
     */
    public function employeeSearch()
    {
        $term = $this->request->getGet('term') ?? '';
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $dashboard = $this->request->getGet('dashboard') ?? 'all';
        $category = $this->request->getGet('category') ?? 'all';
        $data = $this->searchEmployees($term, $month, $year, $dashboard, $category);
        return $this->response->setJSON(['success' => true, 'data' => $data]);
    }

    /**
     * Get all participant names for the selected dashboard + category
     * This populates the name dropdown automatically after category selection
     */
    public function participantNames()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $dashboard = $this->request->getGet('dashboard') ?? 'all';
        $category = $this->request->getGet('category') ?? 'all';
        $results = $this->fetchParticipantNames($month, $year, $dashboard, $category);
        return $this->response->setJSON(['success' => true, 'data' => $results]);
    }

    /**
     * Get participant profile by name
     */
    public function participantProfile()
    {
        $name = $this->request->getGet('name') ?? '';
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $dashboard = $this->request->getGet('dashboard') ?? 'all';
        $category = $this->request->getGet('category') ?? 'all';
        $profile = $this->fetchParticipantProfile($name, $month, $year, $dashboard, $category);
        return $this->response->setJSON(['success' => true, 'data' => $profile]);
    }

    /**
     * Get employee profile by name
     */
    public function employeeProfile()
    {
        $name = $this->request->getGet('name') ?? '';
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $profile = $this->fetchEmployeeProfile($name, $month, $year);
        return $this->response->setJSON(['success' => true, 'data' => $profile]);
    }

    /**
     * Export data
     */
    public function exportData()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');
        $format = $this->request->getGet('format') ?? 'csv';
        $data = $this->fetchAllData($month, $year);

        if ($format === 'excel') {
            $this->exportExcel($data);
        } else {
            $this->exportCsv($data);
        }
    }

    // ============================================================
    // PRIVATE DATA FETCHING METHODS
    // ============================================================

    /**
     * Detect which months have data for the given year
     */
    private function detectAvailableMonths($year)
    {
        $months = [];

        try {
            $seMonths = $this->db->table('sales_excellence_data')
                ->select('sales_month')
                ->distinct()
                ->where('sales_year', $year)
                ->orderBy('sales_month', 'ASC')
                ->get()
                ->getResultArray();
            foreach ($seMonths as $r) {
                $m = (int)$r['sales_month'];
                if (!in_array($m, $months)) $months[] = $m;
            }
        } catch (\Exception $e) {
            log_message('error', 'detectAvailableMonths - se: ' . $e->getMessage());
        }

        try {
            $tbMonths = $this->db->table('top_branch_data')
                ->select('sales_month')
                ->distinct()
                ->where('sales_year', $year)
                ->orderBy('sales_month', 'ASC')
                ->get()
                ->getResultArray();
            foreach ($tbMonths as $r) {
                $m = (int)$r['sales_month'];
                if (!in_array($m, $months)) $months[] = $m;
            }
        } catch (\Exception $e) {
            log_message('error', 'detectAvailableMonths - tb: ' . $e->getMessage());
        }

        try {
            $ecMonths = $this->db->table('elite_circle_data')
                ->select('sales_month')
                ->distinct()
                ->where('sales_year', $year)
                ->orderBy('sales_month', 'ASC')
                ->get()
                ->getResultArray();
            foreach ($ecMonths as $r) {
                $m = (int)$r['sales_month'];
                if (!in_array($m, $months)) $months[] = $m;
            }
        } catch (\Exception $e) {
            log_message('error', 'detectAvailableMonths - ec: ' . $e->getMessage());
        }

        try {
            $ecrMonths = $this->db->table('elite_circle_records')
                ->select('sales_month')
                ->distinct()
                ->where('sales_year', $year)
                ->orderBy('sales_month', 'ASC')
                ->get()
                ->getResultArray();
            foreach ($ecrMonths as $r) {
                $m = (int)$r['sales_month'];
                if (!in_array($m, $months)) $months[] = $m;
            }
        } catch (\Exception $e) {
            log_message('error', 'detectAvailableMonths - ecr: ' . $e->getMessage());
        }

        sort($months);
        return $months;
    }

    /**
     * Fetch ALL data for the executive dashboard
     */
    private function fetchAllData($month, $year, $dashboard = 'all', $category = 'all', $region = 'all', $search = '')
    {
        return [
            'kpi' => $this->fetchKpiData($month, $year),
            'trend' => $this->fetchPerformanceTrend($year),
            'regional' => $this->fetchRegionalPerformance($month, $year),
            'top10' => $this->fetchTopPerformers($month, $year),
            'achievement_distribution' => $this->fetchAchievementDistribution($month, $year),
            'recent_achievements' => $this->fetchRecentAchievements($month, $year),
            'dashboard_comparison' => $this->fetchDashboardComparison($month, $year),
        ];
    }

    /**
     * Fetch KPI summary data
     */
    private function fetchKpiData($month, $year)
    {
        $month = (int)$month;
        $year = (int)$year;
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear = $year - 1;
        }

        // ===== 1. MONTHLY REVENUE (Base: Sales Excellence Awardee Dashboard) =====
        $totalRevenue = 0;
        $prevRevenue = 0;

        try {
            $seRevenue = $this->db->table('sales_excellence_data')
                ->select('COALESCE(SUM(revenue), 0) as total')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->get()
                ->getRowArray();
            $totalRevenue += (float)($seRevenue['total'] ?? 0);

            $sePrevRevenue = $this->db->table('sales_excellence_data')
                ->select('COALESCE(SUM(revenue), 0) as total')
                ->where('sales_month', $prevMonth)
                ->where('sales_year', $prevYear)
                ->get()
                ->getRowArray();
            $prevRevenue += (float)($sePrevRevenue['total'] ?? 0);
        } catch (\Exception $e) {
            log_message('error', 'fetchKpiData - se revenue: ' . $e->getMessage());
        }

        try {
            $tbRevenue = $this->db->table('top_branch_data')
                ->select('COALESCE(SUM(revenue), 0) as total')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->get()
                ->getRowArray();
            $totalRevenue += (float)($tbRevenue['total'] ?? 0);

            $tbPrevRevenue = $this->db->table('top_branch_data')
                ->select('COALESCE(SUM(revenue), 0) as total')
                ->where('sales_month', $prevMonth)
                ->where('sales_year', $prevYear)
                ->get()
                ->getRowArray();
            $prevRevenue += (float)($tbPrevRevenue['total'] ?? 0);
        } catch (\Exception $e) {
            log_message('error', 'fetchKpiData - tb revenue: ' . $e->getMessage());
        }

        try {
            $ecRevenue = $this->db->table('elite_circle_data')
                ->select('COALESCE(SUM(revenue), 0) as total')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->get()
                ->getRowArray();
            $totalRevenue += (float)($ecRevenue['total'] ?? 0);

            $ecPrevRevenue = $this->db->table('elite_circle_data')
                ->select('COALESCE(SUM(revenue), 0) as total')
                ->where('sales_month', $prevMonth)
                ->where('sales_year', $prevYear)
                ->get()
                ->getRowArray();
            $prevRevenue += (float)($ecPrevRevenue['total'] ?? 0);
        } catch (\Exception $e) {
            log_message('error', 'fetchKpiData - ec revenue: ' . $e->getMessage());
        }

        $revenueChange = 0;
        if ($prevRevenue > 0) {
            $revenueChange = (($totalRevenue - $prevRevenue) / $prevRevenue) * 100;
        }

        // ===== 2. AVS GROWTH (Base: Top Branch Recognition Dashboard - AVS Growth) =====
        $avsGrowth = 0;
        $prevAvsGrowth = 0;

        try {
            $tbGrowth = $this->db->table('top_branch_data')
                ->select('COALESCE(AVG(growth_percent), 0) as avg_growth')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->get()
                ->getRowArray();
            $avsGrowth = (float)($tbGrowth['avg_growth'] ?? 0);

            $tbPrevGrowth = $this->db->table('top_branch_data')
                ->select('COALESCE(AVG(growth_percent), 0) as avg_growth')
                ->where('sales_month', $prevMonth)
                ->where('sales_year', $prevYear)
                ->get()
                ->getRowArray();
            $prevAvsGrowth = (float)($tbPrevGrowth['avg_growth'] ?? 0);
        } catch (\Exception $e) {
            log_message('error', 'fetchKpiData - tb growth: ' . $e->getMessage());
        }

        $avsGrowthChange = 0;
        if ($prevAvsGrowth > 0) {
            $avsGrowthChange = (($avsGrowth - $prevAvsGrowth) / $prevAvsGrowth) * 100;
        }

        // ===== 3. COMBINED VOLUME (Base: Sales Excellence Elite Circle Dashboard - Combined Volume) =====
        $totalVolume = 0;
        $prevVolume = 0;

        try {
            $ecVolume = $this->db->table('elite_circle_data')
                ->select('COALESCE(SUM(volume), 0) as total')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->get()
                ->getRowArray();
            $totalVolume += (float)($ecVolume['total'] ?? 0);

            $ecPrevVolume = $this->db->table('elite_circle_data')
                ->select('COALESCE(SUM(volume), 0) as total')
                ->where('sales_month', $prevMonth)
                ->where('sales_year', $prevYear)
                ->get()
                ->getRowArray();
            $prevVolume += (float)($ecPrevVolume['total'] ?? 0);
        } catch (\Exception $e) {
            log_message('error', 'fetchKpiData - ec volume: ' . $e->getMessage());
        }

        try {
            $ecrVolume = $this->db->table('elite_circle_records')
                ->select('COALESCE(SUM(sales_volume), 0) as total')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->get()
                ->getRowArray();
            $totalVolume += (float)($ecrVolume['total'] ?? 0);

            $ecrPrevVolume = $this->db->table('elite_circle_records')
                ->select('COALESCE(SUM(sales_volume), 0) as total')
                ->where('sales_month', $prevMonth)
                ->where('sales_year', $prevYear)
                ->get()
                ->getRowArray();
            $prevVolume += (float)($ecrPrevVolume['total'] ?? 0);
        } catch (\Exception $e) {
            log_message('error', 'fetchKpiData - ecr volume: ' . $e->getMessage());
        }

        $volumeChange = 0;
        if ($prevVolume > 0) {
            $volumeChange = (($totalVolume - $prevVolume) / $prevVolume) * 100;
        }

        // ===== 4. COMBINED CM (Base: Sales Excellence Elite Circle Dashboard - Combined CM) =====
        $totalCm = 0;
        $prevCm = 0;

        try {
            $ecCm = $this->db->table('elite_circle_data')
                ->select('COALESCE(SUM(gross_amount), 0) as total')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->get()
                ->getRowArray();
            $totalCm += (float)($ecCm['total'] ?? 0);

            $ecPrevCm = $this->db->table('elite_circle_data')
                ->select('COALESCE(SUM(gross_amount), 0) as total')
                ->where('sales_month', $prevMonth)
                ->where('sales_year', $prevYear)
                ->get()
                ->getRowArray();
            $prevCm += (float)($ecPrevCm['total'] ?? 0);
        } catch (\Exception $e) {
            log_message('error', 'fetchKpiData - ec cm: ' . $e->getMessage());
        }

        try {
            $ecrCm = $this->db->table('elite_circle_records')
                ->select('COALESCE(SUM(sales_cm), 0) as total')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->get()
                ->getRowArray();
            $totalCm += (float)($ecrCm['total'] ?? 0);

            $ecrPrevCm = $this->db->table('elite_circle_records')
                ->select('COALESCE(SUM(sales_cm), 0) as total')
                ->where('sales_month', $prevMonth)
                ->where('sales_year', $prevYear)
                ->get()
                ->getRowArray();
            $prevCm += (float)($ecrPrevCm['total'] ?? 0);
        } catch (\Exception $e) {
            log_message('error', 'fetchKpiData - ecr cm: ' . $e->getMessage());
        }

        $cmChange = 0;
        if ($prevCm > 0) {
            $cmChange = (($totalCm - $prevCm) / $prevCm) * 100;
        }

        return [
            'total_revenue' => $totalRevenue,
            'revenue_change' => $revenueChange,
            'avs_growth' => $avsGrowth,
            'avs_growth_change' => $avsGrowthChange,
            'total_volume' => $totalVolume,
            'volume_change' => $volumeChange,
            'total_cm' => $totalCm,
            'cm_change' => $cmChange,
        ];
    }

    /**
     * Fetch performance trend data (monthly line chart)
     */
    private function fetchPerformanceTrend($year)
    {
        $year = (int)$year;
        $monthNames = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];

        $seRevenue = array_fill(1, 12, 0);
        $tbGrowth = array_fill(1, 12, 0);
        $ecVolume = array_fill(1, 12, 0);
        $ecCm = array_fill(1, 12, 0);

        // Sales Excellence Revenue by month
        try {
            $seData = $this->db->table('sales_excellence_data')
                ->select('sales_month, COALESCE(SUM(revenue), 0) as total')
                ->where('sales_year', $year)
                ->groupBy('sales_month')
                ->get()
                ->getResultArray();
            foreach ($seData as $r) {
                $m = (int)$r['sales_month'];
                if ($m >= 1 && $m <= 12) $seRevenue[$m] = (float)$r['total'];
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchPerformanceTrend - se: ' . $e->getMessage());
        }

        // Top Branch Growth by month
        try {
            $tbData = $this->db->table('top_branch_data')
                ->select('sales_month, COALESCE(AVG(growth_percent), 0) as avg_growth')
                ->where('sales_year', $year)
                ->groupBy('sales_month')
                ->get()
                ->getResultArray();
            foreach ($tbData as $r) {
                $m = (int)$r['sales_month'];
                if ($m >= 1 && $m <= 12) $tbGrowth[$m] = (float)$r['avg_growth'];
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchPerformanceTrend - tb: ' . $e->getMessage());
        }

        // Elite Circle Volume by month
        try {
            $ecData = $this->db->table('elite_circle_data')
                ->select('sales_month, COALESCE(SUM(volume), 0) as total')
                ->where('sales_year', $year)
                ->groupBy('sales_month')
                ->get()
                ->getResultArray();
            foreach ($ecData as $r) {
                $m = (int)$r['sales_month'];
                if ($m >= 1 && $m <= 12) $ecVolume[$m] = (float)$r['total'];
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchPerformanceTrend - ec: ' . $e->getMessage());
        }

        // Also include elite_circle_records volume
        try {
            $ecrData = $this->db->table('elite_circle_records')
                ->select('sales_month, COALESCE(SUM(sales_volume), 0) as total')
                ->where('sales_year', $year)
                ->groupBy('sales_month')
                ->get()
                ->getResultArray();
            foreach ($ecrData as $r) {
                $m = (int)$r['sales_month'];
                if ($m >= 1 && $m <= 12) $ecVolume[$m] += (float)$r['total'];
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchPerformanceTrend - ecr: ' . $e->getMessage());
        }

        // Elite Circle CM by month (from elite_circle_data gross_amount)
        try {
            $ecCmData = $this->db->table('elite_circle_data')
                ->select('sales_month, COALESCE(SUM(gross_amount), 0) as total')
                ->where('sales_year', $year)
                ->groupBy('sales_month')
                ->get()
                ->getResultArray();
            foreach ($ecCmData as $r) {
                $m = (int)$r['sales_month'];
                if ($m >= 1 && $m <= 12) $ecCm[$m] = (float)$r['total'];
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchPerformanceTrend - ec cm: ' . $e->getMessage());
        }

        // Also include elite_circle_records CM
        try {
            $ecrCmData = $this->db->table('elite_circle_records')
                ->select('sales_month, COALESCE(SUM(sales_cm), 0) as total')
                ->where('sales_year', $year)
                ->groupBy('sales_month')
                ->get()
                ->getResultArray();
            foreach ($ecrCmData as $r) {
                $m = (int)$r['sales_month'];
                if ($m >= 1 && $m <= 12) $ecCm[$m] += (float)$r['total'];
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchPerformanceTrend - ecr cm: ' . $e->getMessage());
        }

        return [
            'categories' => $monthNames,
            'series' => [
                ['name' => 'Sales Excellence Revenue', 'data' => array_values($seRevenue)],
                ['name' => 'Top Branch Growth %', 'data' => array_values($tbGrowth)],
                ['name' => 'Elite Circle Volume', 'data' => array_values($ecVolume)],
                ['name' => 'Elite Circle CM', 'data' => array_values($ecCm)],
            ],
        ];
    }

    /**
     * Fetch dashboard comparison data (radar chart)
     */
    private function fetchDashboardComparison($month, $year)
    {
        $month = (int)$month;
        $year = (int)$year;

        $seCount = 0;
        $tbCount = 0;
        $ecCount = 0;

        try {
            $seCount = (int)$this->db->table('sales_excellence_data')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'fetchDashboardComparison - se: ' . $e->getMessage());
        }

        try {
            $tbCount = (int)$this->db->table('top_branch_data')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'fetchDashboardComparison - tb: ' . $e->getMessage());
        }

        try {
            $ecCount = (int)$this->db->table('elite_circle_data')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'fetchDashboardComparison - ec: ' . $e->getMessage());
        }

        try {
            $ecrCount = (int)$this->db->table('elite_circle_records')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->countAllResults();
            $ecCount += $ecrCount;
        } catch (\Exception $e) {
            log_message('error', 'fetchDashboardComparison - ecr: ' . $e->getMessage());
        }

        return [
            'categories' => ['Sales Excellence', 'Top Branch', 'Elite Circle'],
            'series' => [
                ['name' => 'Participants', 'data' => [$seCount, $tbCount, $ecCount]],
            ],
        ];
    }

    /**
     * Fetch regional performance data
     */
    private function fetchRegionalPerformance($month, $year)
    {
        $month = (int)$month;
        $year = (int)$year;
        $prevMonth = $month - 1;
        $prevYear = $year;
        if ($prevMonth < 1) {
            $prevMonth = 12;
            $prevYear = $year - 1;
        }

        $regions = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];
        $result = [];

        foreach ($regions as $region) {
            $revenue = 0;
            $prevRevenue = 0;
            $growth = 0;
            $prevGrowth = 0;
            $cm = 0;
            $prevCm = 0;
            $volume = 0;
            $prevVolume = 0;

            // Sales Excellence revenue
            try {
                $seRev = $this->db->table('sales_excellence_data')
                    ->select('COALESCE(SUM(revenue), 0) as total')
                    ->where('region', $region)
                    ->where('sales_month', $month)
                    ->where('sales_year', $year)
                    ->get()
                    ->getRowArray();
                $revenue += (float)($seRev['total'] ?? 0);

                $sePrevRev = $this->db->table('sales_excellence_data')
                    ->select('COALESCE(SUM(revenue), 0) as total')
                    ->where('region', $region)
                    ->where('sales_month', $prevMonth)
                    ->where('sales_year', $prevYear)
                    ->get()
                    ->getRowArray();
                $prevRevenue += (float)($sePrevRev['total'] ?? 0);
            } catch (\Exception $e) {
                log_message('error', 'fetchRegionalPerformance - se rev: ' . $e->getMessage());
            }

            // Top Branch revenue + growth
            try {
                $tbRev = $this->db->table('top_branch_data')
                    ->select('COALESCE(SUM(revenue), 0) as total, COALESCE(AVG(growth_percent), 0) as avg_growth')
                    ->where('region', $region)
                    ->where('sales_month', $month)
                    ->where('sales_year', $year)
                    ->get()
                    ->getRowArray();
                $revenue += (float)($tbRev['total'] ?? 0);
                $growth = (float)($tbRev['avg_growth'] ?? 0);

                $tbPrev = $this->db->table('top_branch_data')
                    ->select('COALESCE(SUM(revenue), 0) as total, COALESCE(AVG(growth_percent), 0) as avg_growth')
                    ->where('region', $region)
                    ->where('sales_month', $prevMonth)
                    ->where('sales_year', $prevYear)
                    ->get()
                    ->getRowArray();
                $prevRevenue += (float)($tbPrev['total'] ?? 0);
                $prevGrowth = (float)($tbPrev['avg_growth'] ?? 0);
            } catch (\Exception $e) {
                log_message('error', 'fetchRegionalPerformance - tb: ' . $e->getMessage());
            }

            // Elite Circle revenue + volume + cm
            try {
                $ecData = $this->db->table('elite_circle_data')
                    ->select('COALESCE(SUM(revenue), 0) as rev, COALESCE(SUM(volume), 0) as vol, COALESCE(SUM(gross_amount), 0) as cm')
                    ->where('region', $region)
                    ->where('sales_month', $month)
                    ->where('sales_year', $year)
                    ->get()
                    ->getRowArray();
                $revenue += (float)($ecData['rev'] ?? 0);
                $volume += (float)($ecData['vol'] ?? 0);
                $cm += (float)($ecData['cm'] ?? 0);

                $ecPrev = $this->db->table('elite_circle_data')
                    ->select('COALESCE(SUM(revenue), 0) as rev, COALESCE(SUM(volume), 0) as vol, COALESCE(SUM(gross_amount), 0) as cm')
                    ->where('region', $region)
                    ->where('sales_month', $prevMonth)
                    ->where('sales_year', $prevYear)
                    ->get()
                    ->getRowArray();
                $prevRevenue += (float)($ecPrev['rev'] ?? 0);
                $prevVolume += (float)($ecPrev['vol'] ?? 0);
                $prevCm += (float)($ecPrev['cm'] ?? 0);
            } catch (\Exception $e) {
                log_message('error', 'fetchRegionalPerformance - ec: ' . $e->getMessage());
            }

            // Elite Circle Records volume + cm
            try {
                $ecrData = $this->db->table('elite_circle_records')
                    ->select('COALESCE(SUM(sales_volume), 0) as vol, COALESCE(SUM(sales_cm), 0) as cm')
                    ->where('region', $region)
                    ->where('sales_month', $month)
                    ->where('sales_year', $year)
                    ->get()
                    ->getRowArray();
                $volume += (float)($ecrData['vol'] ?? 0);
                $cm += (float)($ecrData['cm'] ?? 0);

                $ecrPrev = $this->db->table('elite_circle_records')
                    ->select('COALESCE(SUM(sales_volume), 0) as vol, COALESCE(SUM(sales_cm), 0) as cm')
                    ->where('region', $region)
                    ->where('sales_month', $prevMonth)
                    ->where('sales_year', $prevYear)
                    ->get()
                    ->getRowArray();
                $prevVolume += (float)($ecrPrev['vol'] ?? 0);
                $prevCm += (float)($ecrPrev['cm'] ?? 0);
            } catch (\Exception $e) {
                log_message('error', 'fetchRegionalPerformance - ecr: ' . $e->getMessage());
            }

            $result[] = [
                'region' => $region,
                'revenue' => $revenue,
                'prev_revenue' => $prevRevenue,
                'growth' => $growth,
                'prev_growth' => $prevGrowth,
                'cm' => $cm,
                'prev_cm' => $prevCm,
                'volume' => $volume,
                'prev_volume' => $prevVolume,
            ];
        }

        return $result;
    }

    /**
     * Fetch top 10 performers based on LEADERBOARD DASHBOARD POINTS
     * 
     * Points come from the LEADERBOARD DASHBOARD (overall/year-based total_top):
     *   - Sales Excellence: total_top from sales_excellence_leaderboard table
     *     (summed across attainment & margin categories per person)
     *   - Top Branch: total_top computed from top_branch_data
     *     (count of months with score data, same as grandSlamTbData)
     *   - Elite Circle: total_top computed from elite_circle_data + elite_circle_records
     *     (count of months ranked top 3 per region, same as eliteCircleMonthlyRanking)
     * 
     * The person with the highest total points across ALL dashboards
     * is the TOP PERFORMER (matches the Leaderboard Dashboard TOTAL column).
     */
    private function fetchTopPerformers($month, $year)
    {
        $year = (int)$year;
        $regions = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];

        // Aggregate points per person: key = name|region
        $personPoints = [];
        $personInfo = [];

        /**
         * Helper: Add points to a person
         */
        $addPoints = function ($name, $region, $dashboard, $points, $area = '') use (&$personPoints, &$personInfo) {
            if (empty($name) || $points <= 0) return;
            $key = strtoupper(trim($name)) . '|' . strtoupper(trim($region));
            if (!isset($personPoints[$key])) {
                $personPoints[$key] = 0;
                $personInfo[$key] = [
                    'name' => $name,
                    'region' => $region,
                    'area' => $area,
                    'dashboards' => [],
                ];
            }
            $personPoints[$key] += $points;
            if (!in_array($dashboard, $personInfo[$key]['dashboards'])) {
                $personInfo[$key]['dashboards'][] = $dashboard;
            }
        };

        // ============================================================
        // 1. SALES EXCELLENCE - from sales_excellence_leaderboard table
        // Points = total_top (count of top-3 finishes across the year),
        // summed across all categories (attainment + margin)
        // ============================================================
        try {
            $seLeaderboard = $this->db->table('sales_excellence_leaderboard')
                ->select('name, region, area, category, total_top')
                ->where('sales_year', $year)
                ->get()
                ->getResultArray();

            foreach ($seLeaderboard as $r) {
                if (empty($r['name'])) continue;
                $addPoints(
                    $r['name'],
                    $r['region'] ?? '',
                    'Sales Excellence',
                    (int)($r['total_top'] ?? 0),
                    $r['area'] ?? ''
                );
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchTopPerformers - se leaderboard: ' . $e->getMessage());
        }

        // ============================================================
        // 2. TOP BRANCH - computed from top_branch_data
        // Points = count of months with score data (matches grandSlamTbData)
        // ============================================================
        try {
            $tbRecords = $this->db->table('top_branch_data')
                ->where('sales_year', $year)
                ->get()
                ->getResultArray();

            $tbPersons = [];
            foreach ($tbRecords as $r) {
                if (empty($r['name'])) continue;
                $key = strtoupper(trim($r['name'])) . '|' . strtoupper(trim($r['region'] ?? ''));
                if (!isset($tbPersons[$key])) {
                    $tbPersons[$key] = [
                        'name' => $r['name'],
                        'region' => $r['region'] ?? '',
                        'area' => $r['sales_office'] ?? $r['area'] ?? '',
                        'months' => [],
                    ];
                }
                $m = (int)($r['sales_month'] ?? 0);
                if ($m < 1 || $m > 12) continue;
                $score = max((float)($r['attainment_percent'] ?? 0), (float)($r['growth_percent'] ?? 0));
                if ($score > 0) {
                    $tbPersons[$key]['months'][$m] = true;
                }
            }

            foreach ($tbPersons as $key => $p) {
                $addPoints($p['name'], $p['region'], 'Top Branch', count($p['months']), $p['area']);
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchTopPerformers - tb: ' . $e->getMessage());
        }

        // ============================================================
        // 3. ELITE CIRCLE - computed from elite_circle_data
        // Points = count of months ranked top 3 per region (matches eliteCircleMonthlyRanking)
        // ============================================================
        try {
            $ecRecords = $this->db->table('elite_circle_data')
                ->where('sales_year', $year)
                ->get()
                ->getResultArray();

            $ecPersons = [];
            foreach ($ecRecords as $r) {
                if (empty($r['name'])) continue;
                $key = strtoupper(trim($r['name'])) . '|' . strtoupper(trim($r['region'] ?? ''));
                if (!isset($ecPersons[$key])) {
                    $ecPersons[$key] = [
                        'name' => $r['name'],
                        'region' => $r['region'] ?? '',
                        'area' => $r['area'] ?? '',
                        'months' => [],
                    ];
                }
                $m = (int)($r['sales_month'] ?? 0);
                if ($m < 1 || $m > 12) continue;
                $score = (float)($r['volume'] ?? 0);
                if (!isset($ecPersons[$key]['months'][$m]) || $score > $ecPersons[$key]['months'][$m]) {
                    $ecPersons[$key]['months'][$m] = $score;
                }
            }

            // Rank within region per month, count top-3 finishes
            foreach ($regions as $reg) {
                $monthScores = [];
                foreach ($ecPersons as $key => $p) {
                    if (strtoupper($p['region'] ?? '') !== strtoupper($reg)) continue;
                    foreach ($p['months'] as $m => $score) {
                        $monthScores[$m][] = ['key' => $key, 'score' => $score];
                    }
                }
                foreach ($monthScores as $m => $scores) {
                    usort($scores, function ($a, $b) {
                        return $b['score'] <=> $a['score'];
                    });
                    foreach ($scores as $idx => $s) {
                        if ($idx < 3) {
                            $ecPersons[$s['key']]['top_months'][$m] = true;
                        }
                    }
                }
            }

            foreach ($ecPersons as $key => $p) {
                $topCount = isset($p['top_months']) ? count($p['top_months']) : 0;
                if ($topCount > 0) {
                    $addPoints($p['name'], $p['region'], 'Elite Circle', $topCount, $p['area']);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchTopPerformers - ec: ' . $e->getMessage());
        }

        // ============================================================
        // 4. ELITE CIRCLE RECORDS - computed from elite_circle_records
        // Points = count of months ranked top 3 per region
        // ============================================================
        try {
            $ecrRecords = $this->db->table('elite_circle_records')
                ->where('sales_year', $year)
                ->get()
                ->getResultArray();

            $ecrPersons = [];
            foreach ($ecrRecords as $r) {
                if (empty($r['name'])) continue;
                $key = strtoupper(trim($r['name'])) . '|' . strtoupper(trim($r['region'] ?? ''));
                if (!isset($ecrPersons[$key])) {
                    $ecrPersons[$key] = [
                        'name' => $r['name'],
                        'region' => $r['region'] ?? '',
                        'area' => $r['area'] ?? '',
                        'months' => [],
                    ];
                }
                $m = (int)($r['sales_month'] ?? 0);
                if ($m < 1 || $m > 12) continue;
                $score = (float)($r['sales_volume'] ?? 0);
                if (!isset($ecrPersons[$key]['months'][$m]) || $score > $ecrPersons[$key]['months'][$m]) {
                    $ecrPersons[$key]['months'][$m] = $score;
                }
            }

            // Rank within region per month, count top-3 finishes
            foreach ($regions as $reg) {
                $monthScores = [];
                foreach ($ecrPersons as $key => $p) {
                    if (strtoupper($p['region'] ?? '') !== strtoupper($reg)) continue;
                    foreach ($p['months'] as $m => $score) {
                        $monthScores[$m][] = ['key' => $key, 'score' => $score];
                    }
                }
                foreach ($monthScores as $m => $scores) {
                    usort($scores, function ($a, $b) {
                        return $b['score'] <=> $a['score'];
                    });
                    foreach ($scores as $idx => $s) {
                        if ($idx < 3) {
                            $ecrPersons[$s['key']]['top_months'][$m] = true;
                        }
                    }
                }
            }

            foreach ($ecrPersons as $key => $p) {
                $topCount = isset($p['top_months']) ? count($p['top_months']) : 0;
                if ($topCount > 0) {
                    $addPoints($p['name'], $p['region'], 'Elite Circle', $topCount, $p['area']);
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchTopPerformers - ecr: ' . $e->getMessage());
        }

        // ============================================================
        // Build final performer list sorted by total points (descending)
        // ============================================================
        $performers = [];
        foreach ($personPoints as $key => $points) {
            $info = $personInfo[$key];
            if ($points <= 0) continue;
            $performers[] = [
                'name' => $info['name'],
                'region' => $info['region'],
                'area' => $info['area'],
                'dashboard' => implode(' + ', $info['dashboards']),
                'metric' => (string)$points . ' pts',
                'points' => $points,
                'revenue' => 0,
            ];
        }

        // Sort by points descending, then by name ascending for ties
        usort($performers, function ($a, $b) {
            if ($b['points'] !== $a['points']) {
                return $b['points'] - $a['points'];
            }
            return strcmp($a['name'], $b['name']);
        });

        return array_slice($performers, 0, 10);
    }

    /**
     * Fetch achievement distribution
     */
    private function fetchAchievementDistribution($month, $year)
    {
        $month = (int)$month;
        $year = (int)$year;

        $seCount = 0;
        $tbCount = 0;
        $ecCount = 0;

        try {
            $seCount = (int)$this->db->table('sales_excellence_data')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'fetchAchievementDistribution - se: ' . $e->getMessage());
        }

        try {
            $tbCount = (int)$this->db->table('top_branch_data')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'fetchAchievementDistribution - tb: ' . $e->getMessage());
        }

        try {
            $ecCount = (int)$this->db->table('elite_circle_data')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->countAllResults();
        } catch (\Exception $e) {
            log_message('error', 'fetchAchievementDistribution - ec: ' . $e->getMessage());
        }

        try {
            $ecrCount = (int)$this->db->table('elite_circle_records')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->countAllResults();
            $ecCount += $ecrCount;
        } catch (\Exception $e) {
            log_message('error', 'fetchAchievementDistribution - ecr: ' . $e->getMessage());
        }

        return [
            'labels' => ['Sales Excellence', 'Top Branch', 'Elite Circle'],
            'data' => [$seCount, $tbCount, $ecCount],
        ];
    }

    /**
     * Fetch recent achievements
     */
    private function fetchRecentAchievements($month, $year)
    {
        $month = (int)$month;
        $year = (int)$year;
        $achievements = [];

        try {
            $seData = $this->db->table('sales_excellence_data')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->orderBy('attainment_percent', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
            foreach ($seData as $r) {
                $achievements[] = [
                    'name' => $r['name'] ?? 'N/A',
                    'region' => $r['region'] ?? '',
                    'dashboard' => 'Sales Excellence',
                    'achievement' => number_format((float)($r['attainment_percent'] ?? 0), 2) . '% attainment',
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchRecentAchievements - se: ' . $e->getMessage());
        }

        try {
            $tbData = $this->db->table('top_branch_data')
                ->where('sales_month', $month)
                ->where('sales_year', $year)
                ->orderBy('growth_percent', 'DESC')
                ->limit(5)
                ->get()
                ->getResultArray();
            foreach ($tbData as $r) {
                $achievements[] = [
                    'name' => $r['name'] ?? 'N/A',
                    'region' => $r['region'] ?? '',
                    'dashboard' => 'Top Branch',
                    'achievement' => number_format((float)($r['growth_percent'] ?? 0), 2) . '% growth',
                ];
            }
        } catch (\Exception $e) {
            log_message('error', 'fetchRecentAchievements - tb: ' . $e->getMessage());
        }

        return $achievements;
    }

    /**
     * Search employees across all dashboards
     */
    private function searchEmployees($term, $month, $year, $dashboard, $category)
    {
        $results = [];
        if (empty($term)) return $results;

        $upperTerm = strtoupper(trim($term));

        // Search Sales Excellence
        if ($dashboard === 'all' || $dashboard === 'se') {
            try {
                $builder = $this->db->table('sales_excellence_data')
                    ->where('sales_month', $month)
                    ->where('sales_year', $year)
                    ->like('name', $upperTerm);
                if ($category !== 'all') $builder->where('category', $category);
                $rows = $builder->limit(20)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'name' => $r['name'] ?? 'N/A',
                        'region' => $r['region'] ?? '',
                        'dashboard' => 'Sales Excellence',
                        'category' => $r['category'] ?? '',
                        'attainment' => (float)($r['attainment_percent'] ?? 0),
                        'revenue' => (float)($r['revenue'] ?? 0),
                    ];
                }
            } catch (\Exception $e) {
                log_message('error', 'searchEmployees - se: ' . $e->getMessage());
            }
        }

        // Search Top Branch
        if ($dashboard === 'all' || $dashboard === 'tb') {
            try {
                $builder = $this->db->table('top_branch_data')
                    ->where('sales_month', $month)
                    ->where('sales_year', $year)
                    ->like('name', $upperTerm);
                if ($category !== 'all') $builder->where('category', $category);
                $rows = $builder->limit(20)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'name' => $r['name'] ?? 'N/A',
                        'region' => $r['region'] ?? '',
                        'dashboard' => 'Top Branch',
                        'category' => $r['category'] ?? '',
                        'growth' => (float)($r['growth_percent'] ?? 0),
                        'revenue' => (float)($r['revenue'] ?? 0),
                    ];
                }
            } catch (\Exception $e) {
                log_message('error', 'searchEmployees - tb: ' . $e->getMessage());
            }
        }

        // Search Elite Circle
        if ($dashboard === 'all' || $dashboard === 'ec') {
            try {
                $builder = $this->db->table('elite_circle_data')
                    ->where('sales_month', $month)
                    ->where('sales_year', $year)
                    ->like('name', $upperTerm);
                if ($category !== 'all') $builder->where('category', $category);
                $rows = $builder->limit(20)->get()->getResultArray();
                foreach ($rows as $r) {
                    $results[] = [
                        'name' => $r['name'] ?? 'N/A',
                        'region' => $r['region'] ?? '',
                        'dashboard' => 'Elite Circle',
                        'category' => $r['category'] ?? '',
                        'volume' => (float)($r['volume'] ?? 0),
                        'revenue' => (float)($r['revenue'] ?? 0),
                    ];
                }
            } catch (\Exception $e) {
                log_message('error', 'searchEmployees - ec: ' . $e->getMessage());
            }
        }

        return $results;
    }

    /**
     * Get all participant names for the selected dashboard + category
     * This populates the name dropdown automatically after category selection
     */
    private function fetchParticipantNames($month, $year, $dashboard, $category)
    {
        $results = [];

        // Validate dashboard
        if (!in_array($dashboard, ['se', 'tb', 'ec'])) {
            return $results;
        }

        // Validate category per dashboard
        $validCategories = [
            'se' => ['attainment', 'margin'],
            'tb' => ['attainment', 'margin'],
            'ec' => ['volume', 'margin'],
        ];
        if (!isset($validCategories[$dashboard]) || !in_array($category, $validCategories[$dashboard])) {
            return $results;
        }

        // Map dashboard to table
        $tableMap = [
            'se' => 'sales_excellence_data',
            'tb' => 'top_branch_data',
            'ec' => 'elite_circle_data',
        ];
        $table = $tableMap[$dashboard];

        // Dashboard display name
        $dashboardNames = [
            'se' => 'Sales Excellence',
            'tb' => 'Top Branch',
            'ec' => 'Elite Circle',
        ];

        // Query participants for this dashboard + category + month/year
        $query = $this->db->table($table)
            ->where('sales_month', $month)
            ->where('sales_year', $year)
            ->where('category', $category);

        // For Top Branch, order by REGION first, then by the category metric within each region.
        // This matches the TOP BRANCH RECOGNITION DASHBOARD which has separate tables per region,
        // each showing TOP 3 by the category metric (SALES OFFICE column order).
        // If a region has only 1 participant, they are automatically 1st in that region's table.
        if ($dashboard === 'tb') {
            $query->orderBy('region', 'ASC');
            if ($category === 'attainment') {
                $query->orderBy('attainment_percent', 'DESC');
            } else {
                $query->orderBy('growth_percent', 'DESC');
            }
        } else {
            $query->orderBy('name', 'ASC');
        }

        $rows = $query->get()->getResultArray();

        foreach ($rows as $r) {
            $results[] = [
                'name' => $r['name'] ?? 'N/A',
                'sales_office' => $r['sales_office'] ?? $r['area'] ?? '',
                'region' => $r['region'] ?? '',
                'area' => $r['area'] ?? '',
                'position' => $r['position'] ?? '',
                'dashboard' => $dashboardNames[$dashboard],
                'category' => $category,
                'photo' => $this->formatPhotoUrl($r['photo'] ?? null),
                'attainment' => (float)($r['attainment_percent'] ?? 0),
                'growth' => (float)($r['growth_percent'] ?? 0),
                'volume' => (float)($r['volume'] ?? 0),
                'revenue' => (float)($r['revenue'] ?? 0),
            ];
        }

        return $results;
    }

    private function fetchEmployeeProfile($name, $month, $year)
    {
        if (empty($name)) return null;

        $upperName = strtoupper(trim($name));

        $se = $this->db->table('sales_excellence_data')
            ->where('sales_month', $month)->where('sales_year', $year)
            ->like('name', $upperName)
            ->get()->getRowArray();
        if ($se) {
            return [
                'name' => $se['name'],
                'region' => $se['region'] ?? '',
                'dashboard' => 'Sales Excellence',
                'attainment' => (float)($se['attainment_percent'] ?? 0),
                'revenue' => $this->getEffectiveRevenue($se),
                'category' => $se['category'] ?? '',
                'branch' => $se['branch'] ?? '',
                'photo' => $this->formatPhotoUrl($se['photo'] ?? null),
                'position' => $se['position'] ?? '',
                'area' => $se['area'] ?? '',
            ];
        }

        $tb = $this->db->table('top_branch_data')
            ->where('sales_month', $month)->where('sales_year', $year)
            ->like('name', $upperName)
            ->get()->getRowArray();
        if ($tb) {
            return [
                'name' => $tb['name'],
                'region' => $tb['region'] ?? '',
                'dashboard' => 'Top Branch',
                'attainment' => (float)($tb['attainment_percent'] ?? 0),
                'revenue' => $this->getEffectiveRevenue($tb),
                'category' => $tb['category'] ?? '',
                'branch' => $tb['branch'] ?? '',
                'photo' => $this->formatPhotoUrl($tb['photo'] ?? null),
                'position' => $tb['position'] ?? '',
                'area' => $tb['area'] ?? '',
            ];
        }

        $ec = $this->db->table('elite_circle_data')
            ->where('sales_month', $month)->where('sales_year', $year)
            ->like('name', $upperName)
            ->get()->getRowArray();
        if ($ec) {
            return [
                'name' => $ec['name'],
                'region' => $ec['region'] ?? '',
                'dashboard' => 'Elite Circle',
                'volume' => $this->getEffectiveVolume($ec),
                'revenue' => $this->getEffectiveRevenue($ec),
                'category' => $ec['category'] ?? '',
                'branch' => $ec['branch'] ?? '',
                'photo' => $this->formatPhotoUrl($ec['photo'] ?? null),
                'position' => $ec['position'] ?? '',
                'area' => $ec['area'] ?? '',
            ];
        }

        return null;
    }

    private function fetchParticipantProfile($name, $month, $year, $dashboard = 'all', $category = 'all')
    {
        if (empty($name)) return null;

        $upperName = strtoupper(trim($name));

        // Build queries - ALWAYS search ALL tables for complete profile data
        // The dashboard filter determines which tables to search, but we also
        // search other tables to get photos and additional data
        $seQuery = $this->db->table('sales_excellence_data')
            ->where('sales_month', $month)->where('sales_year', $year)
            ->like('name', $upperName);
        $tbQuery = $this->db->table('top_branch_data')
            ->where('sales_month', $month)->where('sales_year', $year)
            ->like('name', $upperName);
        $ecQuery = $this->db->table('elite_circle_data')
            ->where('sales_month', $month)->where('sales_year', $year)
            ->like('name', $upperName);
        $ecSummaryQuery = $this->db->table('elite_circle_summary')
            ->like('top_volume_name', $upperName);

        // Apply category filter to ALL queries regardless of dashboard filter
        if ($category !== 'all') {
            $seQuery->where('category', $category);
            $tbQuery->where('category', $category);
            $ecQuery->where('category', $category);
        }

        // Get ALL matching rows from each table
        $seRows = $seQuery->get()->getResultArray();
        $tbRows = $tbQuery->get()->getResultArray();
        $ecRows = $ecQuery->get()->getResultArray();
        $ecSummaryRows = $ecSummaryQuery->get()->getResultArray();

        // If a specific dashboard is selected, filter the results to only show that dashboard's data
        // But still search ALL tables for photos and profile info
        $seData = $seRows;
        $tbData = $tbRows;
        $ecData = $ecRows;

        if ($dashboard !== 'all') {
            if ($dashboard === 'se') {
                $tbData = [];
                $ecData = [];
            } elseif ($dashboard === 'tb') {
                $seData = [];
                $ecData = [];
            } elseif ($dashboard === 'ec') {
                $seData = [];
                $tbData = [];
            }
        }

        // Use first row from each for primary data, but search ALL rows for photos
        $se = count($seData) > 0 ? $seData[0] : null;
        $tb = count($tbData) > 0 ? $tbData[0] : null;
        $ec = count($ecData) > 0 ? $ecData[0] : null;

        // Search ALL rows (including filtered-out ones) for photos
        $sePhoto = null;
        foreach ($seRows as $r) {
            if (!empty($r['photo'])) { $sePhoto = $r['photo']; break; }
        }
        $tbPhoto = null;
        foreach ($tbRows as $r) {
            if (!empty($r['photo'])) { $tbPhoto = $r['photo']; break; }
        }
        $ecPhoto = null;
        foreach ($ecRows as $r) {
            if (!empty($r['photo'])) { $ecPhoto = $r['photo']; break; }
        }
        // Also search elite_circle_summary for photos
        if (empty($ecPhoto)) {
            foreach ($ecSummaryRows as $r) {
                if (!empty($r['photo'])) { $ecPhoto = $r['photo']; break; }
            }
        }

        // Determine the actual matched name from the first available record
        $matchedName = '';
        if ($se) $matchedName = $se['name'] ?? '';
        if (empty($matchedName) && $tb) $matchedName = $tb['name'] ?? '';
        if (empty($matchedName) && $ec) $matchedName = $ec['name'] ?? '';

        // ===== RANKING: PER DASHBOARD + CATEGORY =====
        // The rank is calculated WITHIN THE REGION within the selected dashboard's category,
        // matching the leaderboard logic (per-region ranking, NOT global).
        $rankStatus = 'No Record';
        $rankPoints = 0;

        if (!empty($matchedName) && $dashboard !== 'all' && $category !== 'all') {
            // Determine the participant's region
            $matchedRegion = '';
            if ($se) $matchedRegion = $se['region'] ?? '';
            if (empty($matchedRegion) && $tb) $matchedRegion = $tb['region'] ?? '';
            if (empty($matchedRegion) && $ec) $matchedRegion = $ec['region'] ?? '';

            // FIRST: Try to get saved rank from leaderboard table (per-region rank)
            if ($dashboard === 'se') {
                $lbRecords = $this->db->table('sales_excellence_leaderboard')
                    ->where('sales_year', $year)
                    ->where('category', $category)
                    ->get()->getResultArray();

                if (!empty($lbRecords)) {
                    // Find this participant's rank from the leaderboard table
                    $monthColMap = [
                        1 => 'jan_rank', 2 => 'feb_rank', 3 => 'mar_rank', 4 => 'apr_rank',
                        5 => 'may_rank', 6 => 'jun_rank', 7 => 'jul_rank', 8 => 'aug_rank',
                        9 => 'sep_rank', 10 => 'oct_rank', 11 => 'nov_rank', 12 => 'dec_rank',
                    ];
                    $targetCol = $monthColMap[(int)$month] ?? null;

                    if ($targetCol) {
                        foreach ($lbRecords as $lr) {
                            if (strtoupper($lr['name'] ?? '') === strtoupper($matchedName) &&
                                strtoupper($lr['region'] ?? '') === strtoupper($matchedRegion)) {
                                $savedRank = (int)($lr[$targetCol] ?? 0);
                                if ($savedRank > 0) {
                                    $rankNum = $savedRank;
                                    $rankPoints = (float)($savedRank);
                                    $suffix = 'th';
                                    if ($rankNum % 10 === 1 && $rankNum % 100 !== 11) $suffix = 'st';
                                    elseif ($rankNum % 10 === 2 && $rankNum % 100 !== 12) $suffix = 'nd';
                                    elseif ($rankNum % 10 === 3 && $rankNum % 100 !== 13) $suffix = 'rd';
                                    $rankStatus = $rankNum . $suffix . ' Place';
                                    break;
                                }
                            }
                        }
                    }
                }
            }

            // Fallback: If no saved leaderboard rank found, compute per-region rank
            if ($rankStatus === 'No Record') {
                $allRecords = [];

                if ($dashboard === 'se') {
                    $allRecords = $this->db->table('sales_excellence_data')
                        ->where('sales_month', $month)->where('sales_year', $year)
                        ->where('category', $category)
                        ->get()->getResultArray();

                    // Sort by category-appropriate metric (within same region only)
                    if ($category === 'attainment') {
                        usort($allRecords, fn($a, $b) => (float)($b['attainment_percent'] ?? 0) <=> (float)($a['attainment_percent'] ?? 0));
                        $rankPoints = (float)($se['attainment_percent'] ?? 0);
                    } else { // margin
                        usort($allRecords, fn($a, $b) => (float)($b['revenue'] ?? 0) <=> (float)($a['revenue'] ?? 0));
                        $rankPoints = (float)($se['revenue'] ?? 0);
                    }
                } elseif ($dashboard === 'tb') {
                    $allRecords = $this->db->table('top_branch_data')
                        ->where('sales_month', $month)->where('sales_year', $year)
                        ->where('category', $category)
                        ->get()->getResultArray();

                    if ($category === 'attainment') {
                        usort($allRecords, fn($a, $b) => (float)($b['attainment_percent'] ?? 0) <=> (float)($a['attainment_percent'] ?? 0));
                        $rankPoints = (float)($tb['attainment_percent'] ?? 0);
                    } else { // growth
                        usort($allRecords, fn($a, $b) => (float)($b['growth_percent'] ?? 0) <=> (float)($a['growth_percent'] ?? 0));
                        $rankPoints = (float)($tb['growth_percent'] ?? 0);
                    }
                } elseif ($dashboard === 'ec') {
                    $allRecords = $this->db->table('elite_circle_data')
                        ->where('sales_month', $month)->where('sales_year', $year)
                        ->where('category', $category)
                        ->get()->getResultArray();

                    if ($category === 'volume') {
                        usort($allRecords, fn($a, $b) => $this->getEffectiveVolume($b) <=> $this->getEffectiveVolume($a));
                        $rankPoints = $this->getEffectiveVolume($ec ?? []);
                    } else { // margin
                        usort($allRecords, fn($a, $b) => (float)($b['gross_amount'] ?? 0) <=> (float)($a['gross_amount'] ?? 0));
                        $rankPoints = (float)($ec['gross_amount'] ?? 0);
                    }
                }

                // Filter to only same region as the participant (per-region ranking)
                // array_values() reindexes the filtered array so the position starts
                // at 0 for the first participant in the region. Without this, the
                // original global sort index (from usort across ALL regions) is used,
                // which produces wrong ranks (e.g. 4th instead of 2nd in a region).
                $sameRegionRecords = array_values(array_filter($allRecords, function($r) use ($matchedRegion) {
                    return strtoupper($r['region'] ?? '') === strtoupper($matchedRegion);
                }));

                // Find this participant's position in the sorted list (within region)
                // Rank is based on their row position in the TOP BRANCH RECOGNITION
                // dashboard table (which has NO RANK column):
                //   1st row = 1st place, 2nd row = 2nd place, 3rd row = 3rd place, etc.
                // If only 1 participant exists in the region, they are automatically 1st.
                $rankNum = 0;
                foreach ($sameRegionRecords as $idx => $r) {
                    if (strtoupper($r['name'] ?? '') === strtoupper($matchedName)) {
                        $rankNum = $idx + 1;
                        break;
                    }
                }

                if ($rankNum > 0) {
                    $suffix = 'th';
                    if ($rankNum % 10 === 1 && $rankNum % 100 !== 11) $suffix = 'st';
                    elseif ($rankNum % 10 === 2 && $rankNum % 100 !== 12) $suffix = 'nd';
                    elseif ($rankNum % 10 === 3 && $rankNum % 100 !== 13) $suffix = 'rd';
                    $rankStatus = $rankNum . $suffix . ' Place';
                }
            }
        }

        // Fallback: use overall leaderboard only if no dashboard/category filter
        if ($rankStatus === 'No Record' && !empty($matchedName)) {
            $lbAll = $this->db->table('sales_excellence_leaderboard')
                ->select('name, SUM(total_top) as total_points')
                ->groupBy('name')
                ->orderBy('total_points', 'DESC')
                ->get()
                ->getResultArray();

            if (!empty($lbAll)) {
                $rankedParticipants = [];
                $currentRank = 1;
                $prevPoints = null;
                foreach ($lbAll as $idx => $p) {
                    $pts = (int)($p['total_points'] ?? 0);
                    if ($prevPoints !== null && $pts < $prevPoints) {
                        $currentRank = $idx + 1;
                    }
                    $rankedParticipants[$p['name']] = [
                        'rank' => $currentRank,
                        'points' => $pts,
                    ];
                    $prevPoints = $pts;
                }

                if (isset($rankedParticipants[$matchedName])) {
                    $rankPoints = $rankedParticipants[$matchedName]['points'];
                    $rankNum = $rankedParticipants[$matchedName]['rank'];
                    $suffix = 'th';
                    if ($rankNum % 10 === 1 && $rankNum % 100 !== 11) $suffix = 'st';
                    elseif ($rankNum % 10 === 2 && $rankNum % 100 !== 12) $suffix = 'nd';
                    elseif ($rankNum % 10 === 3 && $rankNum % 100 !== 13) $suffix = 'rd';
                    $rankStatus = $rankNum . $suffix . ' Place (' . $rankPoints . ' pts)';
                }
            }
        }

        $profile = [
            'name' => !empty($matchedName) ? $matchedName : $name,
            'area' => '',
            'position' => '',
            'region' => '',
            'photo' => null,
            'dashboards' => [],
            'rank_status' => $rankStatus,
            'lb_points' => $rankPoints,
            'assessment_status' => 'No Record',
        ];

        if ($se) {
            $profile['area'] = $se['area'] ?? '';
            $profile['position'] = $se['position'] ?? '';
            $profile['region'] = $se['region'] ?? '';
            $profile['photo'] = $this->formatPhotoUrl($sePhoto);
            $profile['dashboards'][] = [
                'dashboard' => 'SALES EXCELLENCE AWARDEE',
                'attainment' => (float)($se['attainment_percent'] ?? 0),
                'revenue' => $this->getEffectiveRevenue($se),
                'actual_volume' => (float)($se['actual_volume'] ?? 0),
                'budget' => (float)($se['budget'] ?? 0),
                'actual_cm' => (float)($se['actual_cm'] ?? 0),
                'price_lf' => (float)($se['price_lf'] ?? 0),
                'category' => $se['category'] ?? 'attainment',
                'status' => 'Active',
            ];
        }

        if ($tb) {
            if (empty($profile['area'])) $profile['area'] = $tb['area'] ?? '';
            if (empty($profile['position'])) $profile['position'] = $tb['position'] ?? '';
            if (empty($profile['region'])) $profile['region'] = $tb['region'] ?? '';
            if (empty($profile['photo'])) $profile['photo'] = $this->formatPhotoUrl($tbPhoto);
            $profile['dashboards'][] = [
                'dashboard' => 'TOP BRANCH RECOGNITION',
                'attainment' => (float)($tb['attainment_percent'] ?? 0),
                'growth' => (float)($tb['growth_percent'] ?? 0),
                'revenue' => $this->getEffectiveRevenue($tb),
                'current_month' => (float)($tb['current_month'] ?? 0),
                'last_month' => (float)($tb['last_month'] ?? 0),
                'actual' => (float)($tb['actual'] ?? 0),
                'budget' => (float)($tb['budget'] ?? 0),
                'category' => $tb['category'] ?? 'attainment',
                'status' => 'Active',
            ];
        }

        if ($ec) {
            if (empty($profile['area'])) $profile['area'] = $ec['area'] ?? '';
            if (empty($profile['position'])) $profile['position'] = $ec['position'] ?? '';
            if (empty($profile['region'])) $profile['region'] = $ec['region'] ?? '';
            if (empty($profile['photo'])) $profile['photo'] = $this->formatPhotoUrl($ecPhoto);
            $profile['dashboards'][] = [
                'dashboard' => 'ELITE CIRCLE',
                'volume' => $this->getEffectiveVolume($ec),
                'gross_amount' => (float)($ec['gross_amount'] ?? 0),
                'revenue' => $this->getEffectiveRevenue($ec),
                'category' => $ec['category'] ?? 'volume',
                'status' => 'Active',
            ];
        }

        $dashboardCount = count($profile['dashboards']);

        // Only set fallback rank if no leaderboard rank was found
        if ($profile['rank_status'] === 'No Record' || $profile['rank_status'] === '') {
            if ($dashboardCount >= 3) {
                $profile['rank_status'] = 'GRAND SLAM';
            } elseif ($dashboardCount >= 2) {
                $profile['rank_status'] = 'MULTI-DASHBOARD';
            } elseif ($dashboardCount >= 1) {
                $profile['rank_status'] = 'SINGLE DASHBOARD';
            }
        }

        if ($dashboardCount > 0) {
            $profile['assessment_status'] = 'Assessed';
        }

        return $profile;
    }

    /**
     * Get effective revenue from a record (handles different table schemas)
     */
    private function getEffectiveRevenue($record)
    {
        if (empty($record)) return 0;
        return (float)($record['revenue'] ?? 0);
    }

    /**
     * Get effective volume from a record (handles different table schemas)
     */
    private function getEffectiveVolume($record)
    {
        if (empty($record)) return 0;
        return (float)($record['volume'] ?? $record['sales_volume'] ?? 0);
    }

    /**
     * Format photo URL to ensure it's a proper web path
     */
    private function formatPhotoUrl($photo)
    {
        if (empty($photo)) return null;

        // If it already has a scheme, return as-is
        if (strpos($photo, 'http://') === 0 || strpos($photo, 'https://') === 0) {
            return $photo;
        }

        // If it starts with /, it's already a web path
        if (strpos($photo, '/') === 0) {
            return $photo;
        }

        // Otherwise, assume it's in the uploads/photos directory
        return '/uploads/photos/' . $photo;
    }

    private function exportCsv($data)
    {
        $filename = 'executive_dashboard_export_' . date('Ymd_His') . '.csv';
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        fputcsv($output, ['Name', 'Region', 'Dashboard', 'Score', 'Revenue']);

        if (!empty($data['top10'])) {
            foreach ($data['top10'] as $r) {
                fputcsv($output, [$r['name'], $r['region'], $r['dashboard'], $r['metric'], $r['revenue']]);
            }
        }

        fclose($output);
        exit;
    }

    private function exportExcel($data)
    {
        $filename = 'executive_dashboard_export_' . date('Ymd_His') . '.xls';
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        echo "Name\tRegion\tDashboard\tScore\tRevenue\n";
        if (!empty($data['top10'])) {
            foreach ($data['top10'] as $r) {
                echo implode("\t", [$r['name'], $r['region'], $r['dashboard'], $r['metric'], $r['revenue']]) . "\n";
            }
        }
        exit;
    }
}

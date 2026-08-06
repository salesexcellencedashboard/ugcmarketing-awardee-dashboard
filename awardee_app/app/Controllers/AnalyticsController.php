<?php

namespace App\Controllers;

use Config\Database;

class AnalyticsController extends BaseController
{
    protected $db;

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Overview and Analytics Dashboard | AWARDEE System',
            'user' => [
                'fullname' => session()->get('fullname'),
                'role'     => session()->get('role'),
            ],
        ];

        return view('dashboard/analytics', $data);
    }

    /**
     * Upload awardee photo
     */
    public function uploadPhoto()
    {
        $file = $this->request->getFile('photo');
        $dealerName = $this->request->getPost('dealer_name');
        $region = $this->request->getPost('region');
        $type = $this->request->getPost('type'); // 'se', 'tb', 'ec'

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Invalid file upload.'
            ]);
        }

        if ($file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(FCPATH . 'uploads/photos', $newName);

            // Save to database mapping
            $builder = $this->db->table('awardee_photos');
            // Check if table exists
            try {
                $builder->insert([
                    'dealer_name' => $dealerName,
                    'region' => $region,
                    'type' => $type,
                    'photo' => $newName,
                    'created_at' => date('Y-m-d H:i:s'),
                ]);
            } catch (\Exception $e) {
                // Table doesn't exist yet
            }

            return $this->response->setJSON([
                'success' => true,
                'photo' => '/uploads/photos/' . $newName,
                'message' => 'Photo uploaded successfully!'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'error' => 'Upload failed.'
        ]);
    }

    /**
     * Get awardee photos
     */
    public function getPhotos()
    {
        try {
            $builder = $this->db->table('awardee_photos');
            $photos = $builder->get()->getResultArray();
            return $this->response->setJSON([
                'success' => true,
                'data' => $photos
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => true,
                'data' => []
            ]);
        }
    }

    /**
     * AI Insights API - analyzes dashboard data and returns insights
     */
    public function aiInsights()
    {
        $month = $this->request->getGet('month') ?? date('n');
        $year = $this->request->getGet('year') ?? date('Y');

        // Fetch real data from dashboard API
        $dashboardData = $this->getDashboardData($month, $year);

        $insights = $this->generateInsights($dashboardData, $month, $year);

        return $this->response->setJSON([
            'success' => true,
            'insights' => $insights,
            'data_summary' => $dashboardData
        ]);
    }

    private function getDashboardData($month, $year)
    {
        $data = [];

        // Sales Excellence Data
        $data['sales_excellence'] = $this->db->table('sales_excellence_data')
            ->where('sales_month', $month)
            ->where('sales_year', $year)
            ->get()->getResultArray();

        // Top Branch Data
        $data['top_branch'] = $this->db->table('top_branch_data')
            ->where('sales_month', $month)
            ->where('sales_year', $year)
            ->get()->getResultArray();

        // Elite Circle Data
        $data['elite_circle_data'] = $this->db->table('elite_circle_data')
            ->where('sales_month', $month)
            ->where('sales_year', $year)
            ->get()->getResultArray();

        // Leaderboard data (safely handle missing table)
        try {
            $data['leaderboard'] = $this->db->table('leaderboard')
                ->where('year', $year)
                ->orderBy('total_score', 'DESC')
                ->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'getDashboardData - leaderboard: ' . $e->getMessage());
            $data['leaderboard'] = [];
        }

        return $data;
    }

    private function generateInsights($data, $month, $year)
    {
        $insights = [];
        $monthNames = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
        $monthName = $monthNames[(int)$month] ?? date('F');

        // Total Revenue Analysis
        $seRevenue = array_sum(array_column($data['sales_excellence'], 'revenue'));
        $tbRevenue = array_sum(array_column($data['top_branch'], 'revenue'));
        $ecRevenue = array_sum(array_column($data['elite_circle_data'], 'revenue'));
        $totalRevenue = $seRevenue + $tbRevenue + $ecRevenue;

        // Attainment Analysis
        $attainmentValues = array_filter(array_column($data['sales_excellence'], 'attainment_percent'));
        $avgAttainment = count($attainmentValues) > 0 ? array_sum($attainmentValues) / count($attainmentValues) : 0;

        // Top performers
        $topSe = [];
        foreach ($data['sales_excellence'] as $r) {
            $name = $r['name'] ?? 'N/A';
            $att = (float)($r['attainment_percent'] ?? 0);
            if ($att > 0) $topSe[] = ['name' => $name, 'att' => $att, 'region' => $r['region'] ?? ''];
        }
        usort($topSe, fn($a, $b) => $b['att'] <=> $a['att']);

        // Top Branch Growth
        $topTb = [];
        foreach ($data['top_branch'] as $r) {
            $name = $r['name'] ?? 'N/A';
            $growth = (float)($r['growth_percent'] ?? 0);
            if ($growth > 0) $topTb[] = ['name' => $name, 'growth' => $growth, 'region' => $r['region'] ?? ''];
        }
        usort($topTb, fn($a, $b) => $b['growth'] <=> $a['growth']);

        // Elite Circle Analysis
        $totalVolume = array_sum(array_column($data['elite_circle_data'], 'volume'));
        $totalCm = array_sum(array_column($data['elite_circle_data'], 'gross_amount'));

        // Build insights
        if ($totalRevenue > 0) {
            $insights[] = [
                'type' => 'revenue',
                'icon' => '📊',
                'title' => 'Revenue Overview',
                'message' => "Total revenue for {$monthName} {$year} is ₱" . number_format($totalRevenue, 2) . " across all segments. Sales Excellence contributes " . number_format(($seRevenue/$totalRevenue)*100, 1) . "% of total revenue."
            ];
        }

        if ($avgAttainment > 0) {
            $insights[] = [
                'type' => 'attainment',
                'icon' => '🎯',
                'title' => 'Average Attainment',
                'message' => "Average attainment rate across Sales Excellence awardees is " . number_format($avgAttainment, 2) . "%. " . ($avgAttainment > 80 ? "Excellent performance! Teams are exceeding targets." : "There's room for improvement in target achievement.")
            ];
        }

        if (count($topSe) > 0) {
            $top = $topSe[0];
            $insights[] = [
                'type' => 'top_performer',
                'icon' => '🏆',
                'title' => 'Top Sales Excellence Awardee',
                'message' => "{$top['name']} from {$top['region']} leads with " . number_format($top['att'], 2) . "% attainment rate."
            ];
        }

        if (count($topTb) > 0) {
            $top = $topTb[0];
            $insights[] = [
                'type' => 'top_growth',
                'icon' => '📈',
                'title' => 'Top Branch Growth',
                'message' => "{$top['name']} from {$top['region']} shows the highest growth at " . number_format($top['growth'], 2) . "%."
            ];
        }

        if ($totalVolume > 0 || $totalCm > 0) {
            $insights[] = [
                'type' => 'elite_circle',
                'icon' => '💎',
                'title' => 'Elite Circle Performance',
                'message' => "Elite Circle total volume: " . number_format($totalVolume) . " units with ₱" . number_format($totalCm, 2) . " contribution margin."
            ];
        }

        // Leaderboard insight
        if (count($data['leaderboard']) > 0) {
            $topLb = $data['leaderboard'][0];
            $insights[] = [
                'type' => 'leaderboard',
                'icon' => '🏅',
                'title' => 'Leaderboard Leader',
                'message' => "{$topLb['dealer_name']} leads the leaderboard with " . number_format($topLb['total_score'] ?? 0) . " total points for {$year}."
            ];
        }

        // Recommendation
        if ($avgAttainment < 70) {
            $insights[] = [
                'type' => 'recommendation',
                'icon' => '💡',
                'title' => 'AI Recommendation',
                'message' => "Focus on coaching programs for regions with attainment below 70%. Consider incentive programs to boost performance in underperforming areas."
            ];
        } else {
            $insights[] = [
                'type' => 'recommendation',
                'icon' => '🎉',
                'title' => 'AI Recommendation',
                'message' => "Performance is strong! Consider recognizing top achievers with special awards. Maintain momentum with continued support and development programs."
            ];
        }

        // Regional comparison
        $regions = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];
        $regionRevenue = [];
        foreach ($regions as $reg) {
            $rev = 0;
            foreach ($data['sales_excellence'] as $r) {
                if (strtoupper($r['region'] ?? '') === $reg) $rev += (float)($r['revenue'] ?? 0);
            }
            if ($rev > 0) $regionRevenue[$reg] = $rev;
        }
        if (count($regionRevenue) > 0) {
            arsort($regionRevenue);
            $topRegion = array_key_first($regionRevenue);
            $insights[] = [
                'type' => 'regional',
                'icon' => '🗺️',
                'title' => 'Top Performing Region',
                'message' => "{$topRegion} leads in revenue with ₱" . number_format($regionRevenue[$topRegion], 2) . "."
            ];
        }

        return $insights;
    }
}
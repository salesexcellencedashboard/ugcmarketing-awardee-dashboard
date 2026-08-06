<?php

namespace App\Controllers;

use Config\Database;

class LeaderboardController extends BaseController
{
    protected $db = null;
    protected $dbConnected = false;

    public function __construct()
    {
        // Don't connect to database in constructor - use lazy loading
        // This prevents crashes if MySQL is temporarily unavailable
    }

    /**
     * Lazy-load database connection
     */
    private function initDb(): void
    {
        if ($this->db === null) {
            try {
                $this->db = Database::connect();
                $this->dbConnected = true;
            } catch (\Exception $e) {
                log_message('error', 'LeaderboardController DB connect error: ' . $e->getMessage());
                $this->dbConnected = false;
            }
        }
    }

    public function index()
    {
        $data = [
            'pageTitle'      => 'Leaderboard Dashboard - Grand Slam Tracking',
            'user'           => [
                'fullname' => session()->get('fullname'),
                'role'     => session()->get('role'),
            ],
            'availableYears' => $this->getYearsSafe(),
        ];

        return view('leaderboard/index', $data);
    }

    /**
     * Safe version of getYears that won't crash if database is unavailable
     */
    private function getYearsSafe(): array
    {
        try {
            return $this->getYears();
        } catch (\Exception $e) {
            log_message('error', 'getYears error: ' . $e->getMessage());
            $years = [];
            for ($y = 2025; $y <= 2030; $y++) {
                $years[] = $y;
            }
            return $years;
        }
    }

    private function getYears(): array
    {
        $years = [];

        $this->initDb();

        if (!$this->dbConnected || $this->db === null) {
            for ($y = 2025; $y <= 2030; $y++) {
                $years[] = $y;
            }
            return $years;
        }

        try {
            $seYears = $this->db->table('sales_excellence_data')
                ->select('sales_year')
                ->distinct()
                ->orderBy('sales_year', 'DESC')
                ->get()
                ->getResultArray();
            foreach ($seYears as $r) {
                $y = (int) $r['sales_year'];
                if (!in_array($y, $years)) $years[] = $y;
            }
        } catch (\Exception $e) {
            log_message('error', 'getYears - seTable: ' . $e->getMessage());
        }

        try {
            $tbYears = $this->db->table('top_branch_data')
                ->select('sales_year')
                ->distinct()
                ->orderBy('sales_year', 'DESC')
                ->get()
                ->getResultArray();
            foreach ($tbYears as $r) {
                $y = (int) $r['sales_year'];
                if (!in_array($y, $years)) $years[] = $y;
            }
        } catch (\Exception $e) {
            log_message('error', 'getYears - tbTable: ' . $e->getMessage());
        }

        try {
            $ecrYears = $this->db->table('elite_circle_records')
                ->select('sales_year')
                ->distinct()
                ->orderBy('sales_year', 'DESC')
                ->get()
                ->getResultArray();
            foreach ($ecrYears as $r) {
                $y = (int) $r['sales_year'];
                if (!in_array($y, $years)) $years[] = $y;
            }
        } catch (\Exception $e) {
            log_message('error', 'getYears - ecrTable: ' . $e->getMessage());
        }

        $minYear = 2025;
        $maxYear = 2030;
        for ($y = $minYear; $y <= $maxYear; $y++) {
            if (!in_array($y, $years)) $years[] = $y;
        }

        sort($years);
        return $years;
    }

    /**
     * API: Save leaderboard - records RANK per participant per month
     * Processes Sales Excellence (attainment, margin), Top Branch, and Elite Circle data
     */
    public function saveLeaderboard()
    {
        try {
            $this->initDb();
            if (!$this->dbConnected || $this->db === null) {
                return $this->response->setStatusCode(500)->setJSON(['success' => false, 'error' => 'Database connection failed.']);
            }

            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
            }

            $json = $this->request->getJSON(true);
            if (!$json) {
                $json = $this->request->getPost();
            }

            $year = (int) ($json['year'] ?? date('Y'));
            $targetMonth = (int) ($json['month'] ?? 0);

            $monthCols = [
                1 => 'jan_rank', 2 => 'feb_rank', 3 => 'mar_rank', 4 => 'apr_rank',
                5 => 'may_rank', 6 => 'jun_rank', 7 => 'jul_rank', 8 => 'aug_rank',
                9 => 'sep_rank', 10 => 'oct_rank', 11 => 'nov_rank', 12 => 'dec_rank',
            ];
            $regions = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];

            $categories = ['attainment', 'margin'];
            $totalInserted = 0;
            $totalUpdated = 0;
            $now = date('Y-m-d H:i:s');

            // ============================================================
            // 1. SALES EXCELLENCE - Process attainment and margin categories
            // ============================================================
            foreach ($categories as $cat) {
                $builder = $this->db->table('sales_excellence_data')
                    ->where('category', $cat)
                    ->where('sales_year', $year)
                    ->where('sales_month', $targetMonth)
                    ->orderBy('region', 'ASC')
                    ->orderBy('name', 'ASC');

                $records = $builder->get()->getResultArray();
                if (empty($records)) continue;

                $personScores = [];
                foreach ($records as $r) {
                    $reg = $r['region'] ?? '';
                    $key = $r['name'] . '|' . $reg;
                    $score = ($cat === 'margin') ? (float) ($r['actual_cm'] ?? 0) : (float) ($r['attainment_percent'] ?? 0);

                    if (!isset($personScores[$key]) || $score > $personScores[$key]['score']) {
                        $personScores[$key] = [
                            'name' => $r['name'],
                            'region' => $reg,
                            'area' => $r['area'] ?? '',
                            'score' => $score,
                        ];
                    }
                }

                $rankedByRegion = [];
                foreach ($regions as $reg) {
                    $regionScores = [];
                    foreach ($personScores as $key => $ps) {
                        if ($ps['region'] !== $reg) continue;
                        $regionScores[$key] = $ps;
                    }
                    if (empty($regionScores)) continue;
                    usort($regionScores, fn($a, $b) => $b['score'] <=> $a['score']);
                    foreach ($regionScores as $idx => $rs) {
                        $rankedByRegion[$rs['name'] . '|' . $rs['region']] = $idx + 1;
                    }
                }

                $existingRecords = $this->db->table('sales_excellence_leaderboard')
                    ->where('sales_year', $year)->where('category', $cat)->get()->getResultArray();
                $existingByKey = [];
                foreach ($existingRecords as $er) {
                    $existingByKey[$er['name'] . '|' . $er['region']] = $er;
                }

                $targetCol = $monthCols[$targetMonth] ?? null;
                if (!$targetCol) continue;

                foreach ($personScores as $key => $ps) {
                    $rank = $rankedByRegion[$key] ?? 0;

                    if (isset($existingByKey[$key])) {
                        $existing = $existingByKey[$key];
                        $updateData = [
                            'area' => $ps['area'],
                            $targetCol => $rank,
                            'updated_at' => $now,
                        ];

                        $totalTop = 0;
                        foreach ($monthCols as $m => $col) {
                            $r = ($m === $targetMonth) ? $rank : (int) ($existing[$col] ?? 0);
                            if ($r >= 1 && $r <= 3) $totalTop++;
                        }
                        $updateData['total_top'] = $totalTop;

                        $this->db->table('sales_excellence_leaderboard')->where('uuid', $existing['uuid'])->update($updateData);
                        $totalUpdated++;
                    } else {
                        $insertData = [
                            'uuid' => $this->generateUUID(),
                            'name' => $ps['name'],
                            'region' => $ps['region'],
                            'area' => $ps['area'],
                            'category' => $cat,
                            'sales_year' => $year,
                            'total_top' => ($rank >= 1 && $rank <= 3) ? 1 : 0,
                            'created_at' => $now,
                            'updated_at' => $now,
                        ];
                        foreach ($monthCols as $m => $col) {
                            $insertData[$col] = ($m === $targetMonth) ? $rank : 0;
                        }
                        $this->db->table('sales_excellence_leaderboard')->insert($insertData);
                        $totalInserted++;
                    }
                }
            }

            // ============================================================
            // 2. TOP BRANCH - Process attainment and growth categories
            // ============================================================
            $tbCategories = ['attainment', 'growth', 'margin'];
            $tbLeaderboardTable = 'top_branch_leaderboard';

            // Check if table exists
            $tbTableExists = false;
            try {
                $check = $this->db->simpleQuery("SHOW TABLES LIKE '{$tbLeaderboardTable}'");
                $tbTableExists = $this->db->affectedRows() > 0;
            } catch (\Exception $e) {
                log_message('error', 'saveLeaderboard - check tb table: ' . $e->getMessage());
            }

            foreach ($tbCategories as $cat) {
                $builder = $this->db->table('top_branch_data')
                    ->where('category', $cat)
                    ->where('sales_year', $year)
                    ->where('sales_month', $targetMonth)
                    ->orderBy('region', 'ASC')
                    ->orderBy('name', 'ASC');

                $records = $builder->get()->getResultArray();
                if (empty($records)) continue;

                $personScores = [];
                foreach ($records as $r) {
                    $reg = $r['region'] ?? '';
                    $key = $r['name'] . '|' . $reg;
                    $score = (float) ($r['growth_percent'] ?? 0);
                    if (($r['category'] ?? '') === 'attainment' || ($r['category'] ?? '') === '') {
                        $score = (float) ($r['attainment_percent'] ?? 0);
                    }

                    if (!isset($personScores[$key]) || $score > $personScores[$key]['score']) {
                        $personScores[$key] = [
                            'name' => $r['name'],
                            'region' => $reg,
                            'area' => $r['sales_office'] ?? '',
                            'score' => $score,
                        ];
                    }
                }

                $rankedByRegion = [];
                foreach ($regions as $reg) {
                    $regionScores = [];
                    foreach ($personScores as $key => $ps) {
                        if ($ps['region'] !== $reg) continue;
                        $regionScores[$key] = $ps;
                    }
                    if (empty($regionScores)) continue;
                    usort($regionScores, fn($a, $b) => $b['score'] <=> $a['score']);
                    foreach ($regionScores as $idx => $rs) {
                        $rankedByRegion[$rs['name'] . '|' . $rs['region']] = $idx + 1;
                    }
                }

                if ($tbTableExists) {
                    $existingRecords = $this->db->table($tbLeaderboardTable)
                        ->where('sales_year', $year)->where('category', $cat)->get()->getResultArray();
                    $existingByKey = [];
                    foreach ($existingRecords as $er) {
                        $existingByKey[$er['name'] . '|' . $er['region']] = $er;
                    }

                    $targetCol = $monthCols[$targetMonth] ?? null;
                    if (!$targetCol) continue;

                    foreach ($personScores as $key => $ps) {
                        $rank = $rankedByRegion[$key] ?? 0;

                        if (isset($existingByKey[$key])) {
                            $existing = $existingByKey[$key];
                            $updateData = [
                                'area' => $ps['area'],
                                $targetCol => $rank,
                                'updated_at' => $now,
                            ];

                            $totalTop = 0;
                            foreach ($monthCols as $m => $col) {
                                $r = ($m === $targetMonth) ? $rank : (int) ($existing[$col] ?? 0);
                                if ($r >= 1 && $r <= 3) $totalTop++;
                            }
                            $updateData['total_top'] = $totalTop;

                            $this->db->table($tbLeaderboardTable)->where('uuid', $existing['uuid'])->update($updateData);
                            $totalUpdated++;
                        } else {
                            $insertData = [
                                'uuid' => $this->generateUUID(),
                                'name' => $ps['name'],
                                'region' => $ps['region'],
                                'area' => $ps['area'],
                                'category' => $cat,
                                'sales_year' => $year,
                                'total_top' => ($rank >= 1 && $rank <= 3) ? 1 : 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            foreach ($monthCols as $m => $col) {
                                $insertData[$col] = ($m === $targetMonth) ? $rank : 0;
                            }
                            $this->db->table($tbLeaderboardTable)->insert($insertData);
                            $totalInserted++;
                        }
                    }
                }
            }

            // ============================================================
            // 3. ELITE CIRCLE - Process volume and margin categories
            // ============================================================
            $ecCategories = ['volume', 'margin'];
            $ecLeaderboardTable = 'elite_circle_leaderboard';

            $ecTableExists = false;
            try {
                $check = $this->db->simpleQuery("SHOW TABLES LIKE '{$ecLeaderboardTable}'");
                $ecTableExists = $this->db->affectedRows() > 0;
            } catch (\Exception $e) {
                log_message('error', 'saveLeaderboard - check ec table: ' . $e->getMessage());
            }

            foreach ($ecCategories as $cat) {
                $builder = $this->db->table('elite_circle_data')
                    ->where('category', $cat)
                    ->where('sales_year', $year)
                    ->where('sales_month', $targetMonth)
                    ->orderBy('region', 'ASC')
                    ->orderBy('name', 'ASC');

                $records = $builder->get()->getResultArray();
                if (empty($records)) continue;

                $personScores = [];
                foreach ($records as $r) {
                    $reg = $r['region'] ?? '';
                    $key = $r['name'] . '|' . $reg;
                    $score = ($cat === 'margin') ? (float) ($r['gross_amount'] ?? 0) : (float) ($r['volume'] ?? 0);

                    if (!isset($personScores[$key]) || $score > $personScores[$key]['score']) {
                        $personScores[$key] = [
                            'name' => $r['name'],
                            'region' => $reg,
                            'area' => $r['area'] ?? '',
                            'score' => $score,
                        ];
                    }
                }

                $rankedByRegion = [];
                foreach ($regions as $reg) {
                    $regionScores = [];
                    foreach ($personScores as $key => $ps) {
                        if ($ps['region'] !== $reg) continue;
                        $regionScores[$key] = $ps;
                    }
                    if (empty($regionScores)) continue;
                    usort($regionScores, fn($a, $b) => $b['score'] <=> $a['score']);
                    foreach ($regionScores as $idx => $rs) {
                        $rankedByRegion[$rs['name'] . '|' . $rs['region']] = $idx + 1;
                    }
                }

                if ($ecTableExists) {
                    $existingRecords = $this->db->table($ecLeaderboardTable)
                        ->where('sales_year', $year)->where('category', $cat)->get()->getResultArray();
                    $existingByKey = [];
                    foreach ($existingRecords as $er) {
                        $existingByKey[$er['name'] . '|' . $er['region']] = $er;
                    }

                    $targetCol = $monthCols[$targetMonth] ?? null;
                    if (!$targetCol) continue;

                    foreach ($personScores as $key => $ps) {
                        $rank = $rankedByRegion[$key] ?? 0;

                        if (isset($existingByKey[$key])) {
                            $existing = $existingByKey[$key];
                            $updateData = [
                                'area' => $ps['area'],
                                $targetCol => $rank,
                                'updated_at' => $now,
                            ];

                            $totalTop = 0;
                            foreach ($monthCols as $m => $col) {
                                $r = ($m === $targetMonth) ? $rank : (int) ($existing[$col] ?? 0);
                                if ($r >= 1 && $r <= 3) $totalTop++;
                            }
                            $updateData['total_top'] = $totalTop;

                            $this->db->table($ecLeaderboardTable)->where('uuid', $existing['uuid'])->update($updateData);
                            $totalUpdated++;
                        } else {
                            $insertData = [
                                'uuid' => $this->generateUUID(),
                                'name' => $ps['name'],
                                'region' => $ps['region'],
                                'area' => $ps['area'],
                                'category' => $cat,
                                'sales_year' => $year,
                                'total_top' => ($rank >= 1 && $rank <= 3) ? 1 : 0,
                                'created_at' => $now,
                                'updated_at' => $now,
                            ];
                            foreach ($monthCols as $m => $col) {
                                $insertData[$col] = ($m === $targetMonth) ? $rank : 0;
                            }
                            $this->db->table($ecLeaderboardTable)->insert($insertData);
                            $totalInserted++;
                        }
                    }
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Leaderboard saved successfully for Month ' . $targetMonth . '. All data types processed (SE, TB, EC).',
                'inserted' => $totalInserted,
                'updated' => $totalUpdated,
                'year' => $year,
                'month' => $targetMonth,
                'categories' => $categories,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'saveLeaderboard error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Save failed: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * API: Get leaderboard data grouped by region
     * Reads from saved leaderboard table (populated by clicking Save in dashboard)
     * Falls back to auto-calculate only if the saved table is empty
     */
    public function getLeaderboardData()
    {
        $this->initDb();
        if (!$this->dbConnected || $this->db === null) {
            return $this->response->setJSON(['success' => false, 'data' => [], 'message' => 'Database connection failed.']);
        }

        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $category = $this->request->getGet('category') ?? 'attainment';

        $records = $this->db->table('sales_excellence_leaderboard')
            ->where('sales_year', $year)
            ->where('category', $category)
            ->orderBy('region', 'ASC')
            ->orderBy('total_top', 'DESC')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        // If no saved leaderboard data, auto-calculate from source data as fallback
        if (empty($records)) {
            $data = $this->calculateLeaderboardFromSource($year, $category);
            if (!empty($data)) {
                return $this->response->setJSON([
                    'success' => true,
                    'data' => $data,
                    'year' => $year,
                    'message' => 'Auto-calculated from source data.',
                ]);
            }
            
            return $this->response->setJSON([
                'success' => true,
                'data' => [],
                'year' => $year,
                'message' => 'No sales data found for the selected year and category.',
            ]);
        }

        // Process saved records from sales_excellence_leaderboard table
        $monthCols = [
            1 => 'jan_rank', 2 => 'feb_rank', 3 => 'mar_rank', 4 => 'apr_rank',
            5 => 'may_rank', 6 => 'jun_rank', 7 => 'jul_rank', 8 => 'aug_rank',
            9 => 'sep_rank', 10 => 'oct_rank', 11 => 'nov_rank', 12 => 'dec_rank',
        ];
        $regions = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];

        $grouped = [];
        foreach ($regions as $reg) {
            $grouped[$reg] = [];
        }

        foreach ($records as $r) {
            $monthly = [];
            foreach ($monthCols as $m => $col) {
                $monthly[$m] = (int) ($r[$col] ?? 0);
            }

            $reg = $r['region'] ?? '';
            if (!isset($grouped[$reg])) {
                $grouped[$reg] = [];
            }
            $grouped[$reg][] = [
                'id' => count($grouped[$reg]) + 1,
                'name' => $r['name'],
                'region' => $reg,
                'area' => $r['area'] ?? '',
                'monthly' => $monthly,
                'total_top' => (int) ($r['total_top'] ?? 0),
            ];
        }

        // Sort each region: total_top DESC, then by best rank positions (1st > 2nd > 3rd), then name ASC
        foreach ($grouped as $reg => &$items) {
            usort($items, function ($a, $b) {
                if ($b['total_top'] !== $a['total_top']) {
                    return $b['total_top'] - $a['total_top'];
                }

                $firstA = 0; $firstB = 0;
                $secondA = 0; $secondB = 0;
                $thirdA = 0; $thirdB = 0;
                foreach ($a['monthly'] as $m => $r) {
                    if ($r === 1) $firstA++;
                    elseif ($r === 2) $secondA++;
                    elseif ($r === 3) $thirdA++;
                }
                foreach ($b['monthly'] as $m => $r) {
                    if ($r === 1) $firstB++;
                    elseif ($r === 2) $secondB++;
                    elseif ($r === 3) $thirdB++;
                }

                if ($firstB !== $firstA) return $firstB - $firstA;
                if ($secondB !== $secondA) return $secondB - $secondA;
                if ($thirdB !== $thirdA) return $thirdB - $thirdA;

                $sumA = array_sum($a['monthly']);
                $sumB = array_sum($b['monthly']);
                if ($sumA !== $sumB) return $sumA - $sumB;

                return strcmp($a['name'], $b['name']);
            });
            foreach ($items as $i => &$item) {
                $item['id'] = $i + 1;
            }
        }
        unset($items, $item);

        return $this->response->setJSON([
            'success' => true,
            'data' => $grouped,
            'year' => $year,
            'message' => 'Loaded from saved leaderboard data.',
        ]);
    }

    /**
     * Auto-calculate leaderboard rankings from sales_excellence_data on-the-fly
     */
    private function calculateLeaderboardFromSource(int $year, string $category): array
    {
        $monthCols = [
            1 => 'jan_rank', 2 => 'feb_rank', 3 => 'mar_rank', 4 => 'apr_rank',
            5 => 'may_rank', 6 => 'jun_rank', 7 => 'jul_rank', 8 => 'aug_rank',
            9 => 'sep_rank', 10 => 'oct_rank', 11 => 'nov_rank', 12 => 'dec_rank',
        ];
        $regions = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];

        $records = $this->db->table('sales_excellence_data')
            ->where('category', $category)
            ->where('sales_year', $year)
            ->orderBy('region', 'ASC')
            ->orderBy('name', 'ASC')
            ->get()
            ->getResultArray();

        if (empty($records)) {
            return [];
        }

        $personMonths = [];
        foreach ($records as $r) {
            $reg = $r['region'] ?? '';
            $key = $r['name'] . '|' . $reg;
            $recMonth = (int) ($r['sales_month'] ?? 1);
            if ($recMonth < 1 || $recMonth > 12) $recMonth = 1;

            $score = ($category === 'margin') ? (float) ($r['actual_cm'] ?? 0) : (float) ($r['attainment_percent'] ?? 0);

            if (!isset($personMonths[$key])) {
                $personMonths[$key] = [
                    'name' => $r['name'],
                    'region' => $reg,
                    'area' => $r['area'] ?? '',
                    'months' => []
                ];
            }
            if (!isset($personMonths[$key]['months'][$recMonth]) || $score > $personMonths[$key]['months'][$recMonth]) {
                $personMonths[$key]['months'][$recMonth] = $score;
            }
        }

        $allMonths = [];
        foreach ($personMonths as $pm) {
            foreach ($pm['months'] as $m => $s) {
                if (!in_array($m, $allMonths)) $allMonths[] = $m;
            }
        }
        sort($allMonths);

        $rankedByMonth = [];
        foreach ($allMonths as $m) {
            $rankedByMonth[$m] = [];
            foreach ($regions as $reg) {
                $regionScores = [];
                foreach ($personMonths as $key => $pm) {
                    if ($pm['region'] !== $reg) continue;
                    if (!isset($pm['months'][$m])) continue;
                    $regionScores[$key] = [
                        'score' => $pm['months'][$m],
                        'name' => $pm['name'],
                        'region' => $pm['region'],
                        'area' => $pm['area']
                    ];
                }
                if (empty($regionScores)) continue;
                usort($regionScores, fn($a, $b) => $b['score'] <=> $a['score']);
                foreach ($regionScores as $idx => $rs) {
                    $rankedByMonth[$m][$rs['name'] . '|' . $rs['region']] = ['rank' => $idx + 1];
                }
            }
        }

        $grouped = [];
        foreach ($regions as $reg) {
            $grouped[$reg] = [];
        }

        foreach ($personMonths as $key => $pm) {
            $monthly = [];
            foreach ($monthCols as $m => $col) {
                $monthly[$m] = (isset($rankedByMonth[$m][$key])) ? $rankedByMonth[$m][$key]['rank'] : 0;
            }
            $totalTop = 0;
            foreach ($monthly as $r) { if ($r >= 1 && $r <= 3) $totalTop++; }

            $reg = $pm['region'];
            if (!isset($grouped[$reg])) {
                $grouped[$reg] = [];
            }
            $grouped[$reg][] = [
                'id' => count($grouped[$reg]) + 1,
                'name' => $pm['name'],
                'region' => $pm['region'],
                'area' => $pm['area'] ?? '',
                'monthly' => $monthly,
                'total_top' => $totalTop,
            ];
        }

        foreach ($grouped as $reg => &$items) {
            usort($items, function ($a, $b) {
                if ($b['total_top'] !== $a['total_top']) {
                    return $b['total_top'] - $a['total_top'];
                }

                $firstA = 0; $firstB = 0;
                $secondA = 0; $secondB = 0;
                $thirdA = 0; $thirdB = 0;
                foreach ($a['monthly'] as $m => $r) {
                    if ($r === 1) $firstA++;
                    elseif ($r === 2) $secondA++;
                    elseif ($r === 3) $thirdA++;
                }
                foreach ($b['monthly'] as $m => $r) {
                    if ($r === 1) $firstB++;
                    elseif ($r === 2) $secondB++;
                    elseif ($r === 3) $thirdB++;
                }

                if ($firstB !== $firstA) return $firstB - $firstA;
                if ($secondB !== $secondA) return $secondB - $secondA;
                if ($thirdB !== $thirdA) return $thirdB - $thirdA;

                $sumA = array_sum($a['monthly']);
                $sumB = array_sum($b['monthly']);
                if ($sumA !== $sumB) return $sumA - $sumB;

                return strcmp($a['name'], $b['name']);
            });
            foreach ($items as $i => &$item) {
                $item['id'] = $i + 1;
            }
        }
        unset($items, $item);

        return $grouped;
    }

    /**
     * Helper: Generate UUID
     */
    private function generateUUID(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * API: Get Grand Slam data for Sales Excellence
     */
    public function grandSlamSeData()
    {
        $this->initDb();
        if (!$this->dbConnected || $this->db === null) {
            return $this->response->setJSON(['success' => true, 'data' => [], 'year' => date('Y')]);
        }

        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $category = $this->request->getGet('category') ?? 'attainment';

        $builder = $this->db->table('sales_excellence_data')
            ->where('category', $category)
            ->where('sales_year', $year)
            ->orderBy('region', 'ASC')
            ->orderBy('name', 'ASC');

        $records = $builder->get()->getResultArray();

        if (empty($records)) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [],
                'year' => $year,
                'category' => $category,
            ]);
        }

        $persons = [];
        $personsLookup = [];

        foreach ($records as $r) {
            $key = $r['name'] . '|' . ($r['region'] ?? '');
            if (!isset($personsLookup[$key])) {
                $personsLookup[$key] = count($persons);
                $persons[] = [
                    'id' => 0,
                    'name' => $r['name'],
                    'region' => $r['region'] ?? '',
                    'area' => $r['area'] ?? '',
                    'position' => $r['position'] ?? '',
                    'monthly' => [],
                    'total_top' => 0,
                    'total_score' => 0,
                ];
            }
            $idx = $personsLookup[$key];
            $m = (int) ($r['sales_month'] ?? 1);

            if ($category === 'margin') {
                $score = (float) ($r['actual_cm'] ?? 0);
            } else {
                $score = (float) ($r['attainment_percent'] ?? 0);
            }

            if (!isset($persons[$idx]['monthly'][$m]) || $score > $persons[$idx]['monthly'][$m]['score']) {
                $persons[$idx]['monthly'][$m] = [
                    'score' => $score,
                    'volume' => (float) ($r['actual_volume'] ?? 0),
                    'revenue' => (float) ($r['revenue'] ?? 0),
                ];
                $persons[$idx]['total_score'] += $score;
            }
        }

        for ($m = 1; $m <= 12; $m++) {
            $monthScores = [];
            foreach ($persons as $pIdx => $p) {
                if (isset($p['monthly'][$m])) {
                    $monthScores[] = [
                        'idx' => $pIdx,
                        'score' => $p['monthly'][$m]['score'],
                    ];
                }
            }
            usort($monthScores, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            foreach ($monthScores as $rank => $ms) {
                $pIdx = $ms['idx'];
                $persons[$pIdx]['monthly'][$m]['rank'] = $rank + 1;
                $persons[$pIdx]['monthly'][$m]['is_top'] = ($rank < 3);
            }
        }

        foreach ($persons as $pIdx => $p) {
            $topCount = 0;
            foreach ($p['monthly'] as $mData) {
                if (isset($mData['rank']) && $mData['rank'] <= 3) {
                    $topCount++;
                }
            }
            $persons[$pIdx]['total_top'] = $topCount;
        }

        usort($persons, function ($a, $b) {
            if ($b['total_top'] !== $a['total_top']) {
                return $b['total_top'] - $a['total_top'];
            }
            if ($b['total_score'] !== $a['total_score']) {
                return $b['total_score'] <=> $a['total_score'];
            }
            return strcmp($a['name'], $b['name']);
        });

        foreach ($persons as $idx => $p) {
            $persons[$idx]['id'] = $idx + 1;
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $persons,
            'year' => $year,
            'category' => $category,
        ]);
    }

    /**
     * API: Get Grand Slam data for Top Branch Recognition
     */
    public function grandSlamTbData()
    {
        $this->initDb();
        if (!$this->dbConnected || $this->db === null) {
            return $this->response->setJSON(['success' => true, 'data' => [], 'year' => date('Y')]);
        }

        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $category = $this->request->getGet('category') ?? '';

        $builder = $this->db->table('top_branch_data')
            ->where('sales_year', $year);

        if (!empty($category)) {
            $builder->where('category', $category);
        }

        $builder->orderBy('region', 'ASC')
            ->orderBy('name', 'ASC');

        $records = $builder->get()->getResultArray();

        if (empty($records)) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [],
                'year' => $year,
            ]);
        }

        $persons = [];
        $personsLookup = [];

        $scoreField = ($category === 'margin') ? 'growth_percent' : 'attainment_percent';

        foreach ($records as $r) {
            $key = $r['name'] . '|' . ($r['region'] ?? '');
            if (!isset($personsLookup[$key])) {
                $personsLookup[$key] = count($persons);
                $persons[] = [
                    'id' => 0,
                    'name' => $r['name'],
                    'region' => $r['region'] ?? '',
                    'sales_office' => $r['sales_office'] ?? '',
                    'area' => $r['area'] ?? '',
                    'position' => $r['position'] ?? '',
                    'monthly' => [],
                    'total_top' => 0,
                    'total_score' => 0,
                ];
            }
            $idx = $personsLookup[$key];
            $m = (int) ($r['sales_month'] ?? 1);

            $score = (float) ($r[$scoreField] ?? 0);

            if (!isset($persons[$idx]['monthly'][$m]) || $score > $persons[$idx]['monthly'][$m]['score']) {
                $persons[$idx]['monthly'][$m] = [
                    'score' => $score,
                    'actual' => (float) ($r['actual'] ?? 0),
                    'revenue' => (float) ($r['revenue'] ?? 0),
                ];
                $persons[$idx]['total_score'] += $score;
            }
        }

        // NO RANKING for Top Branch Recognition - just count months with data
        foreach ($persons as $pIdx => $p) {
            $dataCount = 0;
            foreach ($p['monthly'] as $mData) {
                if ($mData['score'] > 0) {
                    $dataCount++;
                }
            }
            $persons[$pIdx]['total_top'] = $dataCount;
        }

        usort($persons, function ($a, $b) {
            if ($b['total_top'] !== $a['total_top']) {
                return $b['total_top'] - $a['total_top'];
            }
            return strcmp($a['name'], $b['name']);
        });

        foreach ($persons as $idx => $p) {
            $persons[$idx]['id'] = $idx + 1;
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $persons,
            'year' => $year,
        ]);
    }

    /**
     * API: Get available years
     */
    public function availableYears()
    {
        $this->initDb();
        $years = [];

        if (!$this->dbConnected || $this->db === null) {
            return $this->response->setJSON([]);
        }

        $ecrYears = $this->db->table('elite_circle_records')
            ->select('sales_year')
            ->distinct()
            ->orderBy('sales_year', 'DESC')
            ->get()
            ->getResultArray();
        foreach ($ecrYears as $r) {
            $y = (int) $r['sales_year'];
            if (!in_array($y, $years)) $years[] = $y;
        }

        $seYears = $this->db->table('sales_excellence_data')
            ->select('sales_year')
            ->distinct()
            ->orderBy('sales_year', 'DESC')
            ->get()
            ->getResultArray();
        foreach ($seYears as $r) {
            $y = (int) $r['sales_year'];
            if (!in_array($y, $years)) $years[] = $y;
        }

        $tbYears = $this->db->table('top_branch_data')
            ->select('sales_year')
            ->distinct()
            ->orderBy('sales_year', 'DESC')
            ->get()
            ->getResultArray();
        foreach ($tbYears as $r) {
            $y = (int) $r['sales_year'];
            if (!in_array($y, $years)) $years[] = $y;
        }

        if (empty($years)) {
            $years[] = (int) date('Y');
        }

        sort($years);
        return $this->response->setJSON($years);
    }

    /**
     * API: Get Grand Slam data for Elite Circle (Regional summary - legacy)
     */
    public function grandSlamEcData()
    {
        $this->initDb();
        if (!$this->dbConnected || $this->db === null) {
            return $this->response->setJSON(['success' => true, 'data' => [], 'year' => date('Y')]);
        }

        $year = (int) ($this->request->getGet('year') ?? date('Y'));

        $builder = $this->db->table('elite_circle_summary ec')
            ->select('ec.*')
            ->like('ec.quarter_year', (string) $year)
            ->orderBy('ec.total_volume', 'DESC');

        $records = $builder->get()->getResultArray();

        $participants = [];
        foreach ($records as $r) {
            $region = $r['region'];
            if (isset($participants[$region])) {
                $participants[$region]['total_volume'] += (float) ($r['total_volume'] ?? 0);
                $participants[$region]['total_cm'] += (float) ($r['total_cm'] ?? 0);
                if ((float)($r['top_volume_value'] ?? 0) > $participants[$region]['top_volume_value']) {
                    $participants[$region]['top_volume_name'] = $r['top_volume_name'] ?? '';
                    $participants[$region]['top_volume_value'] = (float) ($r['top_volume_value'] ?? 0);
                }
                if ((float)($r['top_cm_value'] ?? 0) > $participants[$region]['top_cm_value']) {
                    $participants[$region]['top_cm_name'] = $r['top_cm_name'] ?? '';
                    $participants[$region]['top_cm_value'] = (float) ($r['top_cm_value'] ?? 0);
                }
            } else {
                $participants[$region] = [
                    'id' => 0,
                    'name' => $region,
                    'region' => $region,
                    'total_volume' => (float) ($r['total_volume'] ?? 0),
                    'total_cm' => (float) ($r['total_cm'] ?? 0),
                    'top_volume_name' => $r['top_volume_name'] ?? '',
                    'top_volume_value' => (float) ($r['top_volume_value'] ?? 0),
                    'top_cm_name' => $r['top_cm_name'] ?? '',
                    'top_cm_value' => (float) ($r['top_cm_value'] ?? 0),
                    'monthly' => [],
                    'total_top' => 0,
                ];
            }
        }

        $result = [];
        foreach (array_values($participants) as $idx => $p) {
            $p['id'] = $idx + 1;
            $result[] = $p;
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $result,
            'year' => $year,
        ]);
    }

    /**
     * API: Sales Excellence Elite Circle - Individual Monthly Ranking
     */
    public function eliteCircleMonthlyRanking()
    {
        $this->initDb();
        if (!$this->dbConnected || $this->db === null) {
            return $this->response->setJSON(['success' => true, 'data' => [], 'year' => date('Y')]);
        }

        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $category = $this->request->getGet('category') ?? 'volume_contributor';

        $scoreField = 'volume';
        $categoryFilter = 'volume';
        if ($category === 'cm_per_region' || $category === 'margin') {
            $scoreField = 'gross_amount';
            $categoryFilter = 'margin';
        }

        $builder = $this->db->table('elite_circle_data ecd')
            ->select('ecd.*')
            ->where('ecd.sales_year', $year)
            ->where('ecd.category', $categoryFilter)
            ->orderBy('ecd.region', 'ASC')
            ->orderBy('ecd.name', 'ASC');

        $records = $builder->get()->getResultArray();

        if (empty($records)) {
            return $this->response->setJSON([
                'success' => true,
                'data' => [],
                'year' => $year,
            ]);
        }

        $persons = [];
        $personsLookup = [];

        foreach ($records as $r) {
            $key = $r['name'] . '|' . ($r['region'] ?? '');
            if (!isset($personsLookup[$key])) {
                $personsLookup[$key] = count($persons);
                $persons[] = [
                    'id' => 0,
                    'name' => $r['name'],
                    'region' => $r['region'] ?? '',
                    'area' => $r['area'] ?? '',
                    'position' => $r['position'] ?? '',
                    'monthly' => [],
                    'total_top' => 0,
                    'total_score' => 0,
                    'total_volume' => 0,
                    'total_cm' => 0,
                ];
            }
            $idx = $personsLookup[$key];
            $m = (int) ($r['sales_month'] ?? 1);
            $vol = (float) ($r['volume'] ?? 0);
            $cm = (float) ($r['gross_amount'] ?? 0);

            $score = ($scoreField === 'gross_amount') ? $cm : $vol;

            if (!isset($persons[$idx]['monthly'][$m]) || $score > $persons[$idx]['monthly'][$m]['score']) {
                $persons[$idx]['monthly'][$m] = [
                    'score' => $score,
                    'volume' => $vol,
                    'cm' => $cm,
                ];
            }
            $persons[$idx]['total_volume'] += $vol;
            $persons[$idx]['total_cm'] += $cm;
        }

        foreach ($persons as $pIdx => $p) {
            $totalScore = 0;
            foreach ($p['monthly'] as $mData) {
                $totalScore += $mData['score'];
            }
            $persons[$pIdx]['total_score'] = $totalScore;
        }

        for ($m = 1; $m <= 12; $m++) {
            $monthScores = [];
            foreach ($persons as $pIdx => $p) {
                if (isset($p['monthly'][$m])) {
                    $monthScores[] = [
                        'idx' => $pIdx,
                        'score' => $p['monthly'][$m]['score'],
                    ];
                }
            }
            usort($monthScores, function ($a, $b) {
                return $b['score'] <=> $a['score'];
            });

            foreach ($monthScores as $rank => $ms) {
                $pIdx = $ms['idx'];
                $persons[$pIdx]['monthly'][$m]['rank'] = $rank + 1;
                $persons[$pIdx]['monthly'][$m]['is_top'] = ($rank < 3);
            }
        }

        foreach ($persons as $pIdx => $p) {
            $topCount = 0;
            foreach ($p['monthly'] as $mData) {
                if (isset($mData['rank']) && $mData['rank'] <= 3) {
                    $topCount++;
                }
            }
            $persons[$pIdx]['total_top'] = $topCount;
        }

        usort($persons, function ($a, $b) {
            if ($b['total_top'] !== $a['total_top']) {
                return $b['total_top'] - $a['total_top'];
            }
            if ($b['total_score'] !== $a['total_score']) {
                return $b['total_score'] <=> $a['total_score'];
            }
            return strcmp($a['name'], $b['name']);
        });

        foreach ($persons as $idx => $p) {
            $persons[$idx]['id'] = $idx + 1;
        }

        return $this->response->setJSON([
            'success' => true,
            'data' => $persons,
            'year' => $year,
            'category' => $category,
        ]);
    }
}
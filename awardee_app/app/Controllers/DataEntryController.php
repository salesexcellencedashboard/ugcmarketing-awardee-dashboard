<?php

namespace App\Controllers;

use Config\Database;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class DataEntryController extends BaseController
{
    protected $db;
    protected $seTable = 'sales_excellence_data';
    protected $tbTable = 'top_branch_data';
    protected $ecTable = 'elite_circle_summary';
    protected $ecRecordsTable = 'elite_circle_records';
    protected $sdTable = 'elite_circle_data';

    public function __construct()
    {
        $this->db = Database::connect();
    }

    public function index()
    {
        $data = [
            'pageTitle' => 'Data Entry Management',
            'user' => [
                'fullname' => session()->get('fullname'),
                'role'     => session()->get('role'),
            ],
        ];
        return view('dataentry/index', $data);
    }

    // ============================================================
    // PHOTO UPLOAD - per record photo (stored in DB, NOT exposed in data table)
    // ============================================================

    public function uploadEntryPhoto()
    {
        try {
            $file = $this->request->getFile('photo');
            $uuid = $this->request->getPost('uuid');
            $type = $this->request->getPost('type'); // 'se', 'tb', 'ec'

            if (!$file || !$file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Invalid file upload.'
                ]);
            }

            if (!$uuid) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Record UUID is required.'
                ]);
            }

            if (!$type || !in_array($type, ['se', 'tb', 'ec'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Invalid record type. Use se, tb, or ec.'
                ]);
            }

            // Determine which table to update
            $tableMap = [
                'se' => $this->seTable,
                'tb' => $this->tbTable,
                'ec' => $this->ecTable,
            ];
            $table = $tableMap[$type];

            // Verify record exists
            $existing = $this->db->table($table)->where('uuid', $uuid)->get()->getRowArray();
            if (!$existing) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Record not found.'
                ]);
            }

            // Ensure upload directory exists
            $uploadDir = FCPATH . 'uploads/photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            // Generate unique filename
            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);

            // Save photo filename to DB only (not exposed in data tables)
            $this->db->table($table)->where('uuid', $uuid)->update([
                'photo' => $newName,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'success' => true,
                'photo' => '/uploads/photos/' . $newName,
                'message' => 'Photo uploaded successfully!'
            ]);
        } catch (\Exception $e) {
            log_message('error', 'uploadEntryPhoto error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Upload failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function getEntryPhoto($uuid)
    {
        try {
            // Search across all three tables
            $tables = [
                'se' => $this->seTable,
                'tb' => $this->tbTable,
                'ec' => $this->sdTable,
            ];

            foreach ($tables as $type => $table) {
                $record = $this->db->table($table)
                    ->select('photo')
                    ->where('uuid', $uuid)
                    ->get()
                    ->getRowArray();

                if ($record && !empty($record['photo'])) {
                    return $this->response->setJSON([
                        'success' => true,
                        'photo' => '/uploads/photos/' . $record['photo'],
                        'type' => $type,
                    ]);
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'photo' => null,
                'message' => 'No photo found for this record.'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => $e->getMessage(),
            ]);
        }
    }

    // ============================================================
    // CLEAR ALL DATA - Comprehensive reset
    // ============================================================

    public function clearAllData()
    {
        try {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
            }

            $tables = [
                $this->seTable,
                $this->tbTable,
                $this->ecTable,
                $this->ecRecordsTable,
                $this->sdTable,
                'sales_excellence_leaderboard',
                'leaderboard',
                'analytics_sessions',
                'sales_records',
                'awardees',
                'grand_slam_awardees',
                'awardee_photos',
            ];

            $cleared = [];
            foreach ($tables as $table) {
                try {
                    // Check if table exists first
                    $check = $this->db->simpleQuery("SHOW TABLES LIKE '{$table}'");
                    if ($this->db->affectedRows() > 0) {
                        $this->db->table($table)->truncate();
                        $cleared[] = $table;
                    }
                } catch (\Exception $e) {
                    log_message('error', "Failed to truncate table {$table}: " . $e->getMessage());
                }
            }

            // Also delete uploaded photos files
            $this->cleanUploadedPhotos();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'All data cleared successfully (' . count($cleared) . ' tables truncated).',
                'tables_cleared' => $cleared,
            ]);
        } catch (\Exception $e) {
            log_message('error', 'clearAllData error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Clear all data failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function clearDataByType($type)
    {
        try {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
            }

            $tables = [];
            $typeLabel = '';

            switch ($type) {
                case 'se':
                    $tables = [$this->seTable, 'sales_excellence_leaderboard'];
                    $typeLabel = 'Sales Excellence';
                    break;
                case 'tb':
                    $tables = [$this->tbTable];
                    $typeLabel = 'Top Branch Recognition';
                    break;
                case 'ec':
                    $tables = [$this->ecTable, $this->ecRecordsTable, $this->sdTable];
                    $typeLabel = 'Elite Circle';
                    break;
                default:
                    return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid type. Use se, tb, or ec.']);
            }

            $cleared = [];
            foreach ($tables as $table) {
                try {
                    $check = $this->db->simpleQuery("SHOW TABLES LIKE '{$table}'");
                    if ($this->db->affectedRows() > 0) {
                        $this->db->table($table)->truncate();
                        $cleared[] = $table;
                    }
                } catch (\Exception $e) {
                    log_message('error', "Failed to truncate table {$table}: " . $e->getMessage());
                }
            }

            // Also clear elite_circle_summary if clearing se or tb since they feed into it
            if (in_array($type, ['se', 'tb'])) {
                try {
                    $this->db->table($this->ecTable)->truncate();
                    $cleared[] = $this->ecTable . ' (regenerated from source data)';
                } catch (\Exception $e) {
                    log_message('error', "Failed to truncate {$this->ecTable}: " . $e->getMessage());
                }
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => "All {$typeLabel} data cleared successfully.",
                'tables_cleared' => $cleared,
            ]);
        } catch (\Exception $e) {
            log_message('error', "clearDataByType({$type}) error: " . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Clear data failed: ' . $e->getMessage(),
            ]);
        }
    }

    private function cleanUploadedPhotos()
    {
        try {
            $photoDir = FCPATH . 'uploads/photos/';
            if (is_dir($photoDir)) {
                $files = glob($photoDir . '*');
                foreach ($files as $file) {
                    if (is_file($file) && basename($file) !== 'index.html') {
                        @unlink($file);
                    }
                }
            }
        } catch (\Exception $e) {
            log_message('error', 'cleanUploadedPhotos error: ' . $e->getMessage());
        }
    }

    // ============================================================
    // EXCEL EXPORT - per dashboard type
    // ============================================================

    public function exportExcel($type)
    {
        // Map frontend tab names to internal type identifiers
        $typeMap = [
            'primeBended' => 'se',
            'primeSpandrel' => 'tb',
            'steelDeck' => 'ec',
        ];
        $internalType = $typeMap[$type] ?? $type;
        
        if (!in_array($internalType, ['se', 'tb', 'ec'])) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid export type']);
        }

        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $month = (int) ($this->request->getGet('month') ?? date('n'));
        $monthLabel = date('F', mktime(0, 0, 0, $month, 1));

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        if ($internalType === 'se') {
            $this->buildSalesExcellenceExport($sheet, $year, $month, $monthLabel);
        } elseif ($internalType === 'tb') {
            $this->buildTopBranchExport($sheet, $year, $month, $monthLabel);
        } elseif ($internalType === 'ec') {
            $this->buildEliteCircleExport($sheet, $year, $month, $monthLabel);
        }

        $filename = '';
        if ($internalType === 'se') $filename = "SALES_EXCELLENCE_AWARDEE_DATA_{$monthLabel}_{$year}";
        elseif ($internalType === 'tb') $filename = "TOP_BRANCH_RECOGNITION_DATA_{$monthLabel}_{$year}";
        elseif ($internalType === 'ec') $filename = "ELITE_CIRCLE_DATA_{$monthLabel}_{$year}";

        $writer = new Xlsx($spreadsheet);

        // Set headers for download
        $this->response->setHeader('Content-Type', 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        $this->response->setHeader('Content-Disposition', 'attachment;filename="' . $filename . '.xlsx"');
        $this->response->setHeader('Cache-Control', 'max-age=0');

        // Write to output
        ob_start();
        $writer->save('php://output');
        $content = ob_get_clean();

        return $this->response->setBody($content);
    }

    private function buildSalesExcellenceExport($sheet, $year, $month, $monthLabel)
    {
        // Title
        $sheet->setCellValue('A1', "SALES EXCELLENCE AWARDEE DATA - {$monthLabel} {$year}");
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        // Headers
        $headers = ['ID', 'Region', 'Name', 'Area', 'Position', 'Category', '% Attainment', 'Actual Volume', 'Budget', 'Revenue', 'Actual CM', 'Price/LF', 'Margin %', 'Growth %', 'Created At'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '3', $h);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF374151');
            $sheet->getStyle($col . '3')->getFont()->getColor()->setARGB('FFFFFFFF');
            $col++;
        }

        // Data
        $data = $this->db->table($this->seTable)
            ->orderBy('region', 'ASC')
            ->orderBy('attainment_percent', 'DESC')
            ->get()
            ->getResultArray();

        $row = 4;
        foreach ($data as $r) {
            $sheet->setCellValue('A' . $row, $r['id']);
            $sheet->setCellValue('B' . $row, $r['region']);
            $sheet->setCellValue('C' . $row, $r['name']);
            $sheet->setCellValue('D' . $row, $r['area']);
            $sheet->setCellValue('E' . $row, $r['position']);
            $sheet->setCellValue('F' . $row, $r['category']);
            $sheet->setCellValue('G' . $row, (float)$r['attainment_percent']);
            $sheet->setCellValue('H' . $row, (float)$r['actual_volume']);
            $sheet->setCellValue('I' . $row, (float)$r['budget']);
            $sheet->setCellValue('J' . $row, (float)$r['revenue']);
            $sheet->setCellValue('K' . $row, (float)$r['actual_cm']);
            $sheet->setCellValue('L' . $row, (float)$r['price_lf']);
            $sheet->setCellValue('M' . $row, (float)$r['margin']);
            $sheet->setCellValue('N' . $row, (float)$r['growth']);
            $sheet->setCellValue('O' . $row, $r['created_at']);
            $row++;
        }

        // Auto-size columns
        foreach (range('A', 'O') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    private function buildTopBranchExport($sheet, $year, $month, $monthLabel)
    {
        $sheet->setCellValue('A1', "TOP BRANCH RECOGNITION DATA - {$monthLabel} {$year}");
        $sheet->mergeCells('A1:O1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $headers = ['ID', 'Region', 'Sales Office', 'Name', 'Area', 'Position', 'Category', 'Growth %', '% Attainment', 'Actual', 'Budget', 'Last Month', 'Current Month', 'Revenue', 'Created At'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '3', $h);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF374151');
            $sheet->getStyle($col . '3')->getFont()->getColor()->setARGB('FFFFFFFF');
            $col++;
        }

        $data = $this->db->table($this->tbTable)
            ->orderBy('region', 'ASC')
            ->orderBy('growth_percent', 'DESC')
            ->get()
            ->getResultArray();

        $row = 4;
        foreach ($data as $r) {
            $sheet->setCellValue('A' . $row, $r['id']);
            $sheet->setCellValue('B' . $row, $r['region']);
            $sheet->setCellValue('C' . $row, $r['sales_office']);
            $sheet->setCellValue('D' . $row, $r['name']);
            $sheet->setCellValue('E' . $row, $r['area']);
            $sheet->setCellValue('F' . $row, $r['position']);
            $sheet->setCellValue('G' . $row, $r['category']);
            $sheet->setCellValue('H' . $row, (float)$r['growth_percent']);
            $sheet->setCellValue('I' . $row, (float)$r['attainment_percent']);
            $sheet->setCellValue('J' . $row, (float)$r['actual']);
            $sheet->setCellValue('K' . $row, (float)$r['budget']);
            $sheet->setCellValue('L' . $row, (float)$r['last_month']);
            $sheet->setCellValue('M' . $row, (float)$r['current_month']);
            $sheet->setCellValue('N' . $row, (float)$r['revenue']);
            $sheet->setCellValue('O' . $row, $r['created_at']);
            $row++;
        }

        foreach (range('A', 'O') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    private function buildEliteCircleExport($sheet, $year, $month, $monthLabel)
    {
        $sheet->setCellValue('A1', "ELITE CIRCLE SUMMARY DATA - {$monthLabel} {$year}");
        $sheet->mergeCells('A1:N1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

        $headers = ['ID', 'Quarter/Year', 'Region', 'Top Vol Name', 'Top Vol Area', 'Top Vol Position', 'Top Vol Value', 'Top CM Name', 'Top CM Area', 'Top CM Position', 'Top CM Value', 'Total Volume', 'Total CM', 'Generated At'];
        $col = 'A';
        foreach ($headers as $h) {
            $sheet->setCellValue($col . '3', $h);
            $sheet->getStyle($col . '3')->getFont()->setBold(true);
            $sheet->getStyle($col . '3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setARGB('FF374151');
            $sheet->getStyle($col . '3')->getFont()->getColor()->setARGB('FFFFFFFF');
            $col++;
        }

        $data = $this->db->table($this->ecTable)
            ->orderBy('quarter_year', 'DESC')
            ->orderBy('region', 'ASC')
            ->get()
            ->getResultArray();

        $row = 4;
        foreach ($data as $r) {
            $sheet->setCellValue('A' . $row, $r['id']);
            $sheet->setCellValue('B' . $row, $r['quarter_year']);
            $sheet->setCellValue('C' . $row, $r['region']);
            $sheet->setCellValue('D' . $row, $r['top_volume_name']);
            $sheet->setCellValue('E' . $row, $r['top_volume_area']);
            $sheet->setCellValue('F' . $row, $r['top_volume_position']);
            $sheet->setCellValue('G' . $row, (float)$r['top_volume_value']);
            $sheet->setCellValue('H' . $row, $r['top_cm_name']);
            $sheet->setCellValue('I' . $row, $r['top_cm_area']);
            $sheet->setCellValue('J' . $row, $r['top_cm_position']);
            $sheet->setCellValue('K' . $row, (float)$r['top_cm_value']);
            $sheet->setCellValue('L' . $row, (float)$r['total_volume']);
            $sheet->setCellValue('M' . $row, (float)$r['total_cm']);
            $sheet->setCellValue('N' . $row, $r['generated_at']);
            $row++;
        }

        foreach (range('A', 'N') as $c) {
            $sheet->getColumnDimension($c)->setAutoSize(true);
        }
    }

    // ============================================================
    // HELPER METHODS
    // ============================================================

    private function generateUUID(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Save an uploaded photo file and return the stored filename
     * Returns null if no file uploaded or invalid
     */
    private function saveUploadedPhoto($fieldName = 'photo')
    {
        try {
            $file = $this->request->getFile($fieldName);
            if (!$file || !$file->isValid() || $file->hasMoved()) {
                return null;
            }

            $uploadDir = FCPATH . 'uploads/photos/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $newName = $file->getRandomName();
            $file->move($uploadDir, $newName);
            return $newName;
        } catch (\Exception $e) {
            log_message('error', 'saveUploadedPhoto error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get request data from either JSON or form data (multipart)
     */
    private function getRequestData()
    {
        // Check if this is a multipart/form-data request (file upload)
        $contentType = $this->request->getHeaderLine('Content-Type');
        if (strpos($contentType, 'multipart/form-data') !== false) {
            return $this->request->getPost();
        }

        // Try JSON first, fall back to POST data
        try {
            $json = $this->request->getJSON(true);
            if ($json) {
                return $json;
            }
        } catch (\Exception $e) {
            // Not JSON - fall through to POST data
        }
        return $this->request->getPost();
    }

    /**
     * Handle photo upload for create operations. Returns photo filename or error response.
     */
    private function handleCreatePhoto(&$data)
    {
        $photoFile = $this->request->getFile('photo');
        if (!$photoFile || !$photoFile->isValid()) {
            return 'Participant photo is required. Please select a photo before saving.';
        }
        $photoName = $this->saveUploadedPhoto('photo');
        if (!$photoName) {
            return 'Failed to save photo. Please try again.';
        }
        $data['photo'] = $photoName;
        return null;
    }

    /**
     * Handle photo upload for update operations. Returns photo filename (existing or new) and deletes old if replaced.
     */
    private function handleUpdatePhoto($existingPhoto)
    {
        $photoFile = $this->request->getFile('photo');
        if ($photoFile && $photoFile->isValid() && !$photoFile->hasMoved()) {
            $newPhoto = $this->saveUploadedPhoto('photo');
            if ($newPhoto) {
                // Delete old photo file if it exists
                if (!empty($existingPhoto)) {
                    $oldPhotoPath = FCPATH . 'uploads/photos/' . $existingPhoto;
                    if (is_file($oldPhotoPath)) {
                        @unlink($oldPhotoPath);
                    }
                }
                return $newPhoto;
            }
        }
        return $existingPhoto;
    }

    // ============================================================
    // SALES EXCELLENCE DATA - CRUD
    // ============================================================

    public function seList()
    {
        $region = $this->request->getGet('region') ?? '';
        $category = $this->request->getGet('category') ?? '';
        $month = $this->request->getGet('month') ?? '';
        $year = $this->request->getGet('year') ?? '';

        $builder = $this->db->table($this->seTable);
        if (!empty($region)) {
            $builder->where('region', $region);
        }
        if (!empty($category)) {
            $builder->where('category', $category);
        }
        if (!empty($month) && is_numeric($month)) {
            $builder->where('sales_month', (int)$month);
        }
        if (!empty($year) && is_numeric($year)) {
            $builder->where('sales_year', (int)$year);
        }
        $data = $builder->orderBy('region', 'ASC')
            ->orderBy('attainment_percent', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
            ->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data),
        ]);
    }

    public function seCreate()
    {
        try {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
            }

            $json = $this->getRequestData();

            $rules = [
                'region' => 'required',
                'name' => 'required',
                'category' => 'required|in_list[attainment,margin]',
            ];

            // Use validation service directly to avoid CI4.7 validateData() issues
            $validation = \Config\Services::validation();
            $validation->setRules($rules);
            if (!$validation->run($json)) {
                log_message('error', 'seCreate validation failed: ' . json_encode($validation->getErrors()) . ' | Data: ' . json_encode($json));
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validation->getErrors(),
                ]);
            }

            $uuid = $this->generateUUID();
            $data = [
                'uuid' => $uuid,
                'region' => $json['region'],
                'name' => $json['name'],
                'area' => $json['area'] ?? '',
                'position' => $json['position'] ?? '',
                'category' => $json['category'],
                'sales_month' => (int) ($json['sales_month'] ?? (int)date('n')),
                'sales_year' => (int) ($json['sales_year'] ?? (int)date('Y')),
                'attainment_percent' => (float) ($json['attainment_percent'] ?? 0),
                'actual_volume' => (float) ($json['actual_volume'] ?? 0),
                'budget' => (float) ($json['budget'] ?? 0),
                'revenue' => (float) ($json['revenue'] ?? 0),
                'actual_cm' => (float) ($json['actual_cm'] ?? 0),
                'price_lf' => (float) ($json['price_lf'] ?? 0),
                'margin' => (float) ($json['margin'] ?? 0),
                'growth' => (float) ($json['growth'] ?? 0),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Photo is REQUIRED for new records
            $photoError = $this->handleCreatePhoto($data);
            if ($photoError) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => $photoError,
                ]);
            }

            $this->db->table($this->seTable)->insert($data);
            $insertId = $this->db->insertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Record created successfully',
                'uuid' => $uuid,
                'id' => $insertId,
                'photo' => '/uploads/photos/' . $data['photo'],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'seCreate error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Save failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function seUpdate($uuid)
    {
        $method = strtolower($this->request->getMethod());
        if ($method !== 'put' && $method !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $json = $this->getRequestData();
        if (!$json) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request data']);
        }

        $existing = $this->db->table($this->seTable)->where('uuid', $uuid)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Record not found']);
        }

        // Handle photo - keep existing if no new photo uploaded
        $photoName = $this->handleUpdatePhoto($existing['photo'] ?? null);

        $updateData = [
            'region' => $json['region'] ?? $existing['region'],
            'name' => $json['name'] ?? $existing['name'],
            'area' => $json['area'] ?? $existing['area'],
            'position' => $json['position'] ?? $existing['position'],
            'category' => $json['category'] ?? $existing['category'],
            'attainment_percent' => (float) ($json['attainment_percent'] ?? $existing['attainment_percent']),
            'actual_volume' => (float) ($json['actual_volume'] ?? $existing['actual_volume']),
            'budget' => (float) ($json['budget'] ?? $existing['budget']),
            'revenue' => (float) ($json['revenue'] ?? $existing['revenue']),
            'actual_cm' => (float) ($json['actual_cm'] ?? $existing['actual_cm']),
            'price_lf' => (float) ($json['price_lf'] ?? $existing['price_lf']),
            'margin' => (float) ($json['margin'] ?? $existing['margin']),
            'growth' => (float) ($json['growth'] ?? $existing['growth']),
            'photo' => $photoName,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table($this->seTable)->where('uuid', $uuid)->update($updateData);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Record updated successfully',
            'photo' => $photoName ? '/uploads/photos/' . $photoName : null,
        ]);
    }

    public function seDelete($uuid)
    {
        if (strtolower($this->request->getMethod()) !== 'delete') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $existing = $this->db->table($this->seTable)->where('uuid', $uuid)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Record not found']);
        }

        // Delete photo file if exists
        if (!empty($existing['photo'])) {
            $photoPath = FCPATH . 'uploads/photos/' . $existing['photo'];
            if (is_file($photoPath)) {
                @unlink($photoPath);
            }
        }

        $recordName = $existing['name'];
        $recordRegion = $existing['region'];
        $recordYear = $existing['sales_year'] ?? null;

        $this->db->table($this->seTable)->where('uuid', $uuid)->delete();

        try {
            $this->db->table('sales_excellence_leaderboard')
                ->where('name', $recordName)
                ->where('region', $recordRegion)
                ->delete();
        } catch (\Exception $e) {
            log_message('error', 'seDelete - leaderboard cascade: ' . $e->getMessage());
        }

        try {
            $this->db->table($this->ecTable)->truncate();
        } catch (\Exception $e) {
            log_message('error', 'seDelete - ec truncate: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Record deleted successfully. Related leaderboard data also cleared.',
        ]);
    }

    // ============================================================
    // TOP BRANCH DATA - CRUD
    // ============================================================

    public function tbList()
    {
        $region = $this->request->getGet('region') ?? '';
        $category = $this->request->getGet('category') ?? '';
        $month = $this->request->getGet('month') ?? '';
        $year = $this->request->getGet('year') ?? '';

        $builder = $this->db->table($this->tbTable);
        if (!empty($region)) {
            $builder->where('region', $region);
        }
        if (!empty($category)) {
            $builder->where('category', $category);
        }
        if (!empty($month) && is_numeric($month)) {
            $builder->where('sales_month', (int)$month);
        }
        if (!empty($year) && is_numeric($year)) {
            $builder->where('sales_year', (int)$year);
        }
        $data = $builder->orderBy('region', 'ASC')
            ->orderBy('growth_percent', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
            ->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data),
        ]);
    }

    public function tbCreate()
    {
        try {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
            }

            $json = $this->getRequestData();

            $rules = [
                'region' => 'required',
                'name' => 'required',
                'category' => 'required|in_list[growth,attainment,margin]',
            ];

            $validation = \Config\Services::validation();
            $validation->setRules($rules);
            if (!$validation->run($json)) {
                log_message('error', 'tbCreate validation failed: ' . json_encode($validation->getErrors()) . ' | Data: ' . json_encode($json));
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validation->getErrors(),
                ]);
            }

            $uuid = $this->generateUUID();
            $data = [
                'uuid' => $uuid,
                'region' => $json['region'],
                'sales_office' => $json['sales_office'] ?? '',
                'name' => $json['name'],
                'area' => $json['area'] ?? '',
                'position' => $json['position'] ?? '',
                'category' => $json['category'],
                'sales_month' => (int) ($json['sales_month'] ?? (int)date('n')),
                'sales_year' => (int) ($json['sales_year'] ?? (int)date('Y')),
                'growth_percent' => (float) ($json['growth_percent'] ?? 0),
                'attainment_percent' => (float) ($json['attainment_percent'] ?? 0),
                'actual' => (float) ($json['actual'] ?? 0),
                'budget' => (float) ($json['budget'] ?? 0),
                'last_month' => (float) ($json['last_month'] ?? 0),
                'current_month' => (float) ($json['current_month'] ?? 0),
                'revenue' => (float) ($json['revenue'] ?? 0),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Photo is REQUIRED for new records
            $photoError = $this->handleCreatePhoto($data);
            if ($photoError) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => $photoError,
                ]);
            }

            $this->db->table($this->tbTable)->insert($data);
            $insertId = $this->db->insertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Record created successfully',
                'uuid' => $uuid,
                'id' => $insertId,
                'photo' => '/uploads/photos/' . $data['photo'],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'tbCreate error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Save failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function tbUpdate($uuid)
    {
        $method = strtolower($this->request->getMethod());
        if ($method !== 'put' && $method !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $json = $this->getRequestData();
        if (!$json) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request data']);
        }

        $existing = $this->db->table($this->tbTable)->where('uuid', $uuid)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Record not found']);
        }

        // Handle photo - keep existing if no new photo uploaded
        $photoName = $this->handleUpdatePhoto($existing['photo'] ?? null);

        $updateData = [
            'region' => $json['region'] ?? $existing['region'],
            'sales_office' => $json['sales_office'] ?? $existing['sales_office'],
            'name' => $json['name'] ?? $existing['name'],
            'area' => $json['area'] ?? $existing['area'],
            'position' => $json['position'] ?? $existing['position'],
            'category' => $json['category'] ?? $existing['category'],
            'growth_percent' => (float) ($json['growth_percent'] ?? $existing['growth_percent']),
            'attainment_percent' => (float) ($json['attainment_percent'] ?? $existing['attainment_percent']),
            'actual' => (float) ($json['actual'] ?? $existing['actual']),
            'budget' => (float) ($json['budget'] ?? $existing['budget']),
            'last_month' => (float) ($json['last_month'] ?? $existing['last_month']),
            'current_month' => (float) ($json['current_month'] ?? $existing['current_month']),
            'revenue' => (float) ($json['revenue'] ?? $existing['revenue']),
            'photo' => $photoName,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table($this->tbTable)->where('uuid', $uuid)->update($updateData);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Record updated successfully',
            'photo' => $photoName ? '/uploads/photos/' . $photoName : null,
        ]);
    }

    public function tbDelete($uuid)
    {
        if (strtolower($this->request->getMethod()) !== 'delete') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $existing = $this->db->table($this->tbTable)->where('uuid', $uuid)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Record not found']);
        }

        // Delete photo file if exists
        if (!empty($existing['photo'])) {
            $photoPath = FCPATH . 'uploads/photos/' . $existing['photo'];
            if (is_file($photoPath)) {
                @unlink($photoPath);
            }
        }

        $this->db->table($this->tbTable)->where('uuid', $uuid)->delete();

        try {
            $this->db->table($this->ecTable)->truncate();
        } catch (\Exception $e) {
            log_message('error', 'tbDelete - ec truncate: ' . $e->getMessage());
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Record deleted successfully. Related summary data also cleared.',
        ]);
    }

    // ============================================================
    // DASHBOARD DATA - Get all data for rendering
    // ============================================================

    public function dashboardData()
    {
        $filterMonth = $this->request->getGet('month') ?? '';
        $filterYear = $this->request->getGet('year') ?? '';

        try {
            $seBuilder = $this->db->table($this->seTable);
            if (!empty($filterMonth) && is_numeric($filterMonth)) {
                $seBuilder->where('sales_month', (int)$filterMonth);
            }
            if (!empty($filterYear) && is_numeric($filterYear)) {
                $seBuilder->where('sales_year', (int)$filterYear);
            }
            $seData = $seBuilder->orderBy('region', 'ASC')
                ->orderBy('attainment_percent', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'dashboardData - seTable: ' . $e->getMessage());
            $seData = [];
        }

        try {
            $tbBuilder = $this->db->table($this->tbTable);
            if (!empty($filterMonth) && is_numeric($filterMonth)) {
                $tbBuilder->where('sales_month', (int)$filterMonth);
            }
            if (!empty($filterYear) && is_numeric($filterYear)) {
                $tbBuilder->where('sales_year', (int)$filterYear);
            }
            $tbData = $tbBuilder->orderBy('region', 'ASC')
                ->orderBy('growth_percent', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'dashboardData - tbTable: ' . $e->getMessage());
            $tbData = [];
        }

        try {
            $ecRecordsBuilder = $this->db->table($this->ecRecordsTable);
            if (!empty($filterMonth) && is_numeric($filterMonth)) {
                $ecRecordsBuilder->where('sales_month', (int)$filterMonth);
            }
            if (!empty($filterYear) && is_numeric($filterYear)) {
                $ecRecordsBuilder->where('sales_year', (int)$filterYear);
            }
            $ecRecords = $ecRecordsBuilder->orderBy('region', 'ASC')
                ->orderBy('sales_volume', 'DESC')
                ->get()
                ->getResultArray();

            $ecData = $this->db->table($this->ecTable)
                ->orderBy('quarter_year', 'DESC')
                ->orderBy('region', 'ASC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'dashboardData - ecTable: ' . $e->getMessage());
            $ecData = [];
        }

        try {
            $sdBuilder = $this->db->table($this->sdTable);
            if (!empty($filterMonth) && is_numeric($filterMonth)) {
                $sdBuilder->where('sales_month', (int)$filterMonth);
            }
            if (!empty($filterYear) && is_numeric($filterYear)) {
                $sdBuilder->where('sales_year', (int)$filterYear);
            }
            $sdData = $sdBuilder->orderBy('region', 'ASC')
                ->orderBy('revenue', 'DESC')
                ->get()
                ->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'dashboardData - sdTable: ' . $e->getMessage());
            $sdData = [];
        }

        $kpis = $this->calculateKPIs($seData, $tbData, $ecData);

        return $this->response->setJSON([
            'success' => true,
            'sales_excellence' => $seData,
            'top_branch' => $tbData,
            'elite_circle' => $ecData,
            'ec_records' => $ecRecords ?? [],
            'elite_circle_data' => $sdData ?? [],
            'kpis' => $kpis,
        ]);
    }

    private function calculateKPIs($seData, $tbData, $ecData)
    {
        $highestAttainment = 0;
        $highestAttainmentRegion = null;
        $highestRevenue = 0;
        $highestRevenueRegion = null;
        $totalRevenue = 0;

        foreach ($seData as $r) {
            if ($r['category'] === 'attainment' && (float)$r['attainment_percent'] > 0) {
                $att = (float)$r['attainment_percent'];
                if ($att > $highestAttainment) {
                    $highestAttainment = $att;
                    $highestAttainmentRegion = $r['region'];
                }
            }
            if ($r['category'] === 'margin' && (float)$r['revenue'] > 0) {
                $rev = (float)$r['revenue'];
                $totalRevenue += $rev;
                if ($rev > $highestRevenue) {
                    $highestRevenue = $rev;
                    $highestRevenueRegion = $r['region'];
                }
            }
        }

        $topRegionAtt = $highestAttainmentRegion ?? '-';
        $topRegionRev = $highestRevenueRegion ?? '-';

        $tbRegionTotals = [];
        $highestGrowth = 0;
        $totalBranches = 0;
        $allGrowth = [];

        foreach ($tbData as $r) {
            $reg = $r['region'];
            if (!isset($tbRegionTotals[$reg])) {
                $tbRegionTotals[$reg] = 0;
            }
            $tbRegionTotals[$reg]++;
            $totalBranches++;
            if ($r['growth_percent'] > $highestGrowth) {
                $highestGrowth = $r['growth_percent'];
            }
            $allGrowth[] = $r['growth_percent'];
        }

        $tbTopRegion = '-';
        $tbMax = 0;
        foreach ($tbRegionTotals as $reg => $count) {
            if ($count > $tbMax) {
                $tbMax = $count;
                $tbTopRegion = $reg;
            }
        }

        $avgGrowth = count($allGrowth) > 0 ? array_sum($allGrowth) / count($allGrowth) : 0;

        $ecCombinedVol = 0;
        $ecCombinedCm = 0;
        $ecTopVolRegion = '-';
        $ecTopCmRegion = '-';
        $ecMaxVol = 0;
        $ecMaxCm = 0;

        foreach ($ecData as $r) {
            $ecCombinedVol += $r['total_volume'];
            $ecCombinedCm += $r['total_cm'];
            if ($r['total_volume'] > $ecMaxVol) {
                $ecMaxVol = $r['total_volume'];
                $ecTopVolRegion = $r['region'];
            }
            if ($r['total_cm'] > $ecMaxCm) {
                $ecMaxCm = $r['total_cm'];
                $ecTopCmRegion = $r['region'];
            }
        }

        return [
            'se' => [
                'topRegion' => $topRegionAtt,
                'topRevenueRegion' => $topRegionRev,
                'highestAttainment' => $highestAttainment,
                'highestRevenue' => $highestRevenue,
                'totalRevenue' => $totalRevenue,
            ],
            'tb' => [
                'topRegion' => $tbTopRegion,
                'highestGrowth' => $highestGrowth,
                'totalBranches' => $totalBranches,
                'avgGrowth' => $avgGrowth,
            ],
            'ec' => [
                'topVolRegion' => $ecTopVolRegion,
                'topCmRegion' => $ecTopCmRegion,
                'combinedVol' => $ecCombinedVol,
                'combinedCm' => $ecCombinedCm,
            ],
        ];
    }

    // ============================================================
    // ELITE CIRCLE DATA - CRUD
    // ============================================================

    public function ecList()
    {
        $quarter = $this->request->getGet('quarter') ?? '';
        $region = $this->request->getGet('region') ?? '';

        $builder = $this->db->table($this->ecTable);
        if (!empty($quarter)) {
            $builder->where('quarter_year', $quarter);
        }
        if (!empty($region)) {
            $builder->where('region', $region);
        }
        $data = $builder->orderBy('quarter_year', 'DESC')
            ->orderBy('region', 'ASC')
            ->get()
            ->getResultArray();

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
            ->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data),
        ]);
    }

    public function ecCreate()
    {
        try {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
            }

            $json = $this->getRequestData();

            $rules = [
                'quarter_year' => 'required',
                'region' => 'required',
            ];

            $validation = \Config\Services::validation();
            $validation->setRules($rules);
            if (!$validation->run($json)) {
                log_message('error', 'ecCreate validation failed: ' . json_encode($validation->getErrors()) . ' | Data: ' . json_encode($json));
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validation->getErrors(),
                ]);
            }

            $uuid = $this->generateUUID();
            $data = [
                'uuid' => $uuid,
                'quarter_year' => $json['quarter_year'],
                'region' => $json['region'],
                'top_volume_name' => $json['top_volume_name'] ?? '',
                'top_volume_area' => $json['top_volume_area'] ?? '',
                'top_volume_position' => $json['top_volume_position'] ?? '',
                'top_volume_value' => (float) ($json['top_volume_value'] ?? 0),
                'top_cm_name' => $json['top_cm_name'] ?? '',
                'top_cm_area' => $json['top_cm_area'] ?? '',
                'top_cm_position' => $json['top_cm_position'] ?? '',
                'top_cm_value' => (float) ($json['top_cm_value'] ?? 0),
                'total_volume' => (float) ($json['total_volume'] ?? 0),
                'total_cm' => (float) ($json['total_cm'] ?? 0),
                'generated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ];

            // Photo is REQUIRED for new records
            $photoError = $this->handleCreatePhoto($data);
            if ($photoError) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => $photoError,
                ]);
            }

            $this->db->table($this->ecTable)->insert($data);
            $insertId = $this->db->insertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Elite Circle record created successfully',
                'uuid' => $uuid,
                'id' => $insertId,
                'photo' => '/uploads/photos/' . $data['photo'],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'ecCreate error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Save failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function ecUpdate($uuid)
    {
        $method = strtolower($this->request->getMethod());
        if ($method !== 'put' && $method !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $json = $this->getRequestData();
        if (!$json) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request data']);
        }

        $existing = $this->db->table($this->ecTable)->where('uuid', $uuid)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Record not found']);
        }

        // Handle photo - keep existing if no new photo uploaded
        $photoName = $this->handleUpdatePhoto($existing['photo'] ?? null);

        $updateData = [
            'quarter_year' => $json['quarter_year'] ?? $existing['quarter_year'],
            'region' => $json['region'] ?? $existing['region'],
            'top_volume_name' => $json['top_volume_name'] ?? $existing['top_volume_name'],
            'top_volume_area' => $json['top_volume_area'] ?? $existing['top_volume_area'],
            'top_volume_position' => $json['top_volume_position'] ?? $existing['top_volume_position'],
            'top_volume_value' => (float) ($json['top_volume_value'] ?? $existing['top_volume_value']),
            'top_cm_name' => $json['top_cm_name'] ?? $existing['top_cm_name'],
            'top_cm_area' => $json['top_cm_area'] ?? $existing['top_cm_area'],
            'top_cm_position' => $json['top_cm_position'] ?? $existing['top_cm_position'],
            'top_cm_value' => (float) ($json['top_cm_value'] ?? $existing['top_cm_value']),
            'total_volume' => (float) ($json['total_volume'] ?? $existing['total_volume']),
            'total_cm' => (float) ($json['total_cm'] ?? $existing['total_cm']),
            'photo' => $photoName,
            'generated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table($this->ecTable)->where('uuid', $uuid)->update($updateData);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Elite Circle record updated successfully',
            'photo' => $photoName ? '/uploads/photos/' . $photoName : null,
        ]);
    }

    public function ecDelete($uuid)
    {
        if (strtolower($this->request->getMethod()) !== 'delete') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $existing = $this->db->table($this->ecTable)->where('uuid', $uuid)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Record not found']);
        }

        // Delete photo file if exists
        if (!empty($existing['photo'])) {
            $photoPath = FCPATH . 'uploads/photos/' . $existing['photo'];
            if (is_file($photoPath)) {
                @unlink($photoPath);
            }
        }

        $this->db->table($this->ecTable)->where('uuid', $uuid)->delete();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Elite Circle record deleted successfully',
        ]);
    }

    // ============================================================
    // ELITE CIRCLE RECORDS (manual data entry per region) - CRUD
    // ============================================================

    public function ecRecordsList()
    {
        $region = $this->request->getGet('region') ?? '';
        $category = $this->request->getGet('category') ?? '';
        $year = (int) ($this->request->getGet('year') ?? date('Y'));
        $month = (int) ($this->request->getGet('month') ?? 0);

        $builder = $this->db->table($this->ecRecordsTable);
        $builder->where('sales_year', $year);
        if (!empty($region)) {
            $builder->where('region', $region);
        }
        if (!empty($category)) {
            $builder->where('category', $category);
        }
        if ($month > 0 && $month <= 12) {
            $builder->where('sales_month', $month);
        }
        $data = $builder->orderBy('region', 'ASC')
            ->orderBy('sales_volume', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data),
        ]);
    }

    public function ecRecordsCreate()
    {
        try {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
            }

            $json = $this->getRequestData();

            $rules = [
                'region' => 'required',
                'name' => 'required',
                'category' => 'required|in_list[volume,cm]',
            ];

            $validation = \Config\Services::validation();
            $validation->setRules($rules);
            if (!$validation->run($json)) {
                log_message('error', 'ecRecordsCreate validation failed: ' . json_encode($validation->getErrors()) . ' | Data: ' . json_encode($json));
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validation->getErrors(),
                ]);
            }

            $uuid = $this->generateUUID();
            $data = [
                'uuid' => $uuid,
                'region' => $json['region'],
                'name' => $json['name'],
                'area' => $json['area'] ?? '',
                'position' => $json['position'] ?? '',
                'category' => $json['category'],
                'sales_month' => (int) ($json['sales_month'] ?? (int)date('n')),
                'sales_year' => (int) ($json['sales_year'] ?? (int)date('Y')),
                'sales_volume' => (float) ($json['sales_volume'] ?? 0),
                'sales_cm' => (float) ($json['sales_cm'] ?? 0),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Photo is REQUIRED for new records
            $photoError = $this->handleCreatePhoto($data);
            if ($photoError) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => $photoError,
                ]);
            }

            $this->db->table($this->ecRecordsTable)->insert($data);
            $insertId = $this->db->insertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Record created successfully',
                'uuid' => $uuid,
                'id' => $insertId,
                'photo' => '/uploads/photos/' . $data['photo'],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'ecRecordsCreate error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Save failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function ecRecordsUpdate($uuid)
    {
        $method = strtolower($this->request->getMethod());
        if ($method !== 'put' && $method !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $json = $this->getRequestData();
        if (!$json) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request data']);
        }

        $existing = $this->db->table($this->ecRecordsTable)->where('uuid', $uuid)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Record not found']);
        }

        // Handle photo - keep existing if no new photo uploaded
        $photoName = $this->handleUpdatePhoto($existing['photo'] ?? null);

        $updateData = [
            'region' => $json['region'] ?? $existing['region'],
            'name' => $json['name'] ?? $existing['name'],
            'area' => $json['area'] ?? $existing['area'],
            'position' => $json['position'] ?? $existing['position'],
            'category' => $json['category'] ?? $existing['category'],
            'sales_month' => (int) ($json['sales_month'] ?? $existing['sales_month']),
            'sales_year' => (int) ($json['sales_year'] ?? $existing['sales_year']),
            'sales_volume' => (float) ($json['sales_volume'] ?? $existing['sales_volume']),
            'sales_cm' => (float) ($json['sales_cm'] ?? $existing['sales_cm']),
            'photo' => $photoName,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table($this->ecRecordsTable)->where('uuid', $uuid)->update($updateData);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Record updated successfully',
            'photo' => $photoName ? '/uploads/photos/' . $photoName : null,
        ]);
    }

    public function ecRecordsDelete($uuid)
    {
        if (strtolower($this->request->getMethod()) !== 'delete') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $existing = $this->db->table($this->ecRecordsTable)->where('uuid', $uuid)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Record not found']);
        }

        // Delete photo file if exists
        if (!empty($existing['photo'])) {
            $photoPath = FCPATH . 'uploads/photos/' . $existing['photo'];
            if (is_file($photoPath)) {
                @unlink($photoPath);
            }
        }

        $this->db->table($this->ecRecordsTable)->where('uuid', $uuid)->delete();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Record deleted successfully',
        ]);
    }

    // ============================================================
    // STEEL DECK DATA - CRUD
    // ============================================================

    public function sdList()
    {
        $region = $this->request->getGet('region') ?? '';
        $month = $this->request->getGet('month') ?? '';
        $year = $this->request->getGet('year') ?? '';

        $builder = $this->db->table($this->sdTable);
        if (!empty($region)) {
            $builder->where('region', $region);
        }
        if (!empty($month) && is_numeric($month)) {
            $builder->where('sales_month', (int)$month);
        }
        if (!empty($year) && is_numeric($year)) {
            $builder->where('sales_year', (int)$year);
        }
        $data = $builder->orderBy('region', 'ASC')
            ->orderBy('revenue', 'DESC')
            ->get()
            ->getResultArray();

        return $this->response
            ->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->setHeader('Pragma', 'no-cache')
            ->setHeader('Expires', 'Thu, 01 Jan 1970 00:00:00 GMT')
            ->setJSON([
            'success' => true,
            'data' => $data,
            'total' => count($data),
        ]);
    }

    public function sdCreate()
    {
        try {
            if (strtolower($this->request->getMethod()) !== 'post') {
                return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
            }

            $json = $this->getRequestData();

            $rules = [
                'region' => 'required',
                'name' => 'required',
            ];

            $validation = \Config\Services::validation();
            $validation->setRules($rules);
            if (!$validation->run($json)) {
                log_message('error', 'sdCreate validation failed: ' . json_encode($validation->getErrors()) . ' | Data: ' . json_encode($json));
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => 'Validation failed',
                    'errors' => $validation->getErrors(),
                ]);
            }

            $uuid = $this->generateUUID();
            $data = [
                'uuid' => $uuid,
                'region' => $json['region'],
                'company' => $json['company'] ?? '',
                'name' => $json['name'],
                'area' => $json['area'] ?? '',
                'position' => $json['position'] ?? '',
                'category' => $json['category'] ?? 'volume',
                'quantity_invoice' => (float) ($json['quantity_invoice'] ?? 0),
                'gross_amount' => (float) ($json['gross_amount'] ?? 0),
                'volume' => (float) ($json['volume'] ?? 0),
                'revenue' => (float) ($json['revenue'] ?? 0),
                'sales_month' => (int) ($json['sales_month'] ?? (int)date('n')),
                'sales_year' => (int) ($json['sales_year'] ?? (int)date('Y')),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            // Photo is REQUIRED for new records
            $photoError = $this->handleCreatePhoto($data);
            if ($photoError) {
                return $this->response->setStatusCode(400)->setJSON([
                    'success' => false,
                    'error' => $photoError,
                ]);
            }

            $this->db->table($this->sdTable)->insert($data);
            $insertId = $this->db->insertID();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Record created successfully',
                'uuid' => $uuid,
                'id' => $insertId,
                'photo' => '/uploads/photos/' . $data['photo'],
            ]);
        } catch (\Exception $e) {
            log_message('error', 'sdCreate error: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'error' => 'Save failed: ' . $e->getMessage(),
            ]);
        }
    }

    public function sdUpdate($uuid)
    {
        $method = strtolower($this->request->getMethod());
        if ($method !== 'put' && $method !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $json = $this->getRequestData();
        if (!$json) {
            return $this->response->setStatusCode(400)->setJSON(['success' => false, 'error' => 'Invalid request data']);
        }

        $existing = $this->db->table($this->sdTable)->where('uuid', $uuid)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Record not found']);
        }

        // Handle photo - keep existing if no new photo uploaded
        $photoName = $this->handleUpdatePhoto($existing['photo'] ?? null);

        $updateData = [
            'region' => $json['region'] ?? $existing['region'],
            'company' => $json['company'] ?? $existing['company'],
            'name' => $json['name'] ?? $existing['name'],
            'area' => $json['area'] ?? $existing['area'],
            'position' => $json['position'] ?? $existing['position'],
            'category' => $json['category'] ?? ($existing['category'] ?? 'volume'),
            'quantity_invoice' => (float) ($json['quantity_invoice'] ?? $existing['quantity_invoice']),
            'gross_amount' => (float) ($json['gross_amount'] ?? $existing['gross_amount']),
            'volume' => (float) ($json['volume'] ?? $existing['volume']),
            'revenue' => (float) ($json['revenue'] ?? $existing['revenue']),
            'photo' => $photoName,
            'updated_at' => date('Y-m-d H:i:s'),
        ];

        $this->db->table($this->sdTable)->where('uuid', $uuid)->update($updateData);

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Record updated successfully',
            'photo' => $photoName ? '/uploads/photos/' . $photoName : null,
        ]);
    }

    public function sdDelete($uuid)
    {
        if (strtolower($this->request->getMethod()) !== 'delete') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $existing = $this->db->table($this->sdTable)->where('uuid', $uuid)->get()->getRowArray();
        if (!$existing) {
            return $this->response->setStatusCode(404)->setJSON(['success' => false, 'error' => 'Record not found']);
        }

        // Delete photo file if exists
        if (!empty($existing['photo'])) {
            $photoPath = FCPATH . 'uploads/photos/' . $existing['photo'];
            if (is_file($photoPath)) {
                @unlink($photoPath);
            }
        }

        $this->db->table($this->sdTable)->where('uuid', $uuid)->delete();

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Record deleted successfully',
        ]);
    }

    // ============================================================
    // ELITE CIRCLE - Generate Quarterly Summary
    // ============================================================

    public function generateEliteCircle()
    {
        if (strtolower($this->request->getMethod()) !== 'post') {
            return $this->response->setStatusCode(405)->setJSON(['success' => false, 'error' => 'Method not allowed']);
        }

        $quarter = $this->request->getPost('quarter') ?? $this->getCurrentQuarter();
        $year = $this->request->getPost('year') ?? date('Y');
        $quarterYear = $quarter . '-' . $year;

        $existing = $this->db->table($this->ecTable)
            ->where('quarter_year', $quarterYear)
            ->countAllResults();

        if ($existing > 0) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Elite Circle summary for ' . $quarterYear . ' already exists. Delete it first to regenerate.',
            ]);
        }

        $seData = $this->db->table($this->seTable)->get()->getResultArray();
        $tbData = $this->db->table($this->tbTable)->get()->getResultArray();

        $regions = ['SOUTH LUZON', 'NORTH & CENTRAL LUZON', 'VISAYAS', 'MINDANAO'];

        $inserted = 0;
        foreach ($regions as $region) {
            $seRegionData = array_filter($seData, function ($r) use ($region) {
                return $r['region'] === $region;
            });
            $tbRegionData = array_filter($tbData, function ($r) use ($region) {
                return $r['region'] === $region;
            });

            $allRegionData = array_merge($seRegionData, $tbRegionData);

            if (empty($allRegionData)) {
                continue;
            }

            $attainmentRecords = array_filter($seRegionData, function ($r) {
                return $r['category'] === 'attainment';
            });
            usort($attainmentRecords, function ($a, $b) {
                return $b['actual_volume'] - $a['actual_volume'];
            });
            $topVol = !empty($attainmentRecords) ? $attainmentRecords[0] : null;

            $marginRecords = array_filter($seRegionData, function ($r) {
                return $r['category'] === 'margin';
            });
            usort($marginRecords, function ($a, $b) {
                return $b['actual_cm'] - $a['actual_cm'];
            });
            $topCm = !empty($marginRecords) ? $marginRecords[0] : null;

            $totalVolume = array_sum(array_column($seRegionData, 'actual_volume'))
                         + array_sum(array_column($tbRegionData, 'actual'));
            $totalCm = array_sum(array_column($seRegionData, 'actual_cm'))
                     + array_sum(array_column($tbRegionData, 'actual_cm'));

            $this->db->table($this->ecTable)->insert([
                'quarter_year' => $quarterYear,
                'region' => $region,
                'top_volume_name' => $topVol['name'] ?? '',
                'top_volume_area' => $topVol['area'] ?? '',
                'top_volume_position' => $topVol['position'] ?? '',
                'top_volume_value' => $topVol['actual_volume'] ?? 0,
                'top_cm_name' => $topCm['name'] ?? '',
                'top_cm_area' => $topCm['area'] ?? '',
                'top_cm_position' => $topCm['position'] ?? '',
                'top_cm_value' => $topCm['actual_cm'] ?? 0,
                'total_volume' => $totalVolume,
                'total_cm' => $totalCm,
                'generated_at' => date('Y-m-d H:i:s'),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
            $inserted++;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Elite Circle summary generated for ' . $quarterYear,
            'regions_generated' => $inserted,
        ]);
    }

    public function deleteEliteCircle()
    {
        try {
            $this->db->table($this->ecTable)->truncate();
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'All Elite Circle data has been deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Exception: ' . $e->getMessage()
            ]);
        }
    }

    private function getCurrentQuarter()
    {
        $month = (int) date('n');
        if ($month <= 3) return 'Q1';
        if ($month <= 6) return 'Q2';
        if ($month <= 9) return 'Q3';
        return 'Q4';
    }
}
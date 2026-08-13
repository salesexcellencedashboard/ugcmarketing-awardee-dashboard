<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes = Services::routes();

if (is_file(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('App\Controllers');
$routes->setDefaultController('AuthController');
$routes->setDefaultMethod('login');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(false);

// Public Routes
$routes->get('/', 'AuthController::login');
$routes->match(['GET', 'POST'], 'login', 'AuthController::login');
$routes->get('logout', 'AuthController::logout');
$routes->match(['GET', 'POST'], 'forgot-password', 'AuthController::forgotPassword');

// Analytics & Awardee Photo Routes (public for API access)
$routes->post('data/upload-photo', 'AnalyticsController::uploadPhoto');
$routes->get('data/get-photos', 'AnalyticsController::getPhotos');
$routes->post('data/upload-entry-photo', 'DataEntryController::uploadEntryPhoto');
$routes->get('data/get-entry-photo/(:any)', 'DataEntryController::getEntryPhoto/$1');
$routes->get('analytics/ai-insights', 'AnalyticsController::aiInsights');

// Clear Data API routes
$routes->post('data/clear-all', 'DataEntryController::clearAllData');
$routes->post('data/clear-type/(:any)', 'DataEntryController::clearDataByType/$1');

// Data Entry CRUD API routes (no auth filter - CSRF handled by fetch)
$routes->get('data/se-list', 'DataEntryController::seList');
$routes->post('data/se-create', 'DataEntryController::seCreate');
$routes->match(['PUT', 'POST'], 'data/se-update/(:any)', 'DataEntryController::seUpdate/$1');
$routes->delete('data/se-delete/(:any)', 'DataEntryController::seDelete/$1');
$routes->get('data/tb-list', 'DataEntryController::tbList');
$routes->post('data/tb-create', 'DataEntryController::tbCreate');
$routes->match(['PUT', 'POST'], 'data/tb-update/(:any)', 'DataEntryController::tbUpdate/$1');
$routes->delete('data/tb-delete/(:any)', 'DataEntryController::tbDelete/$1');
$routes->get('data/dashboard', 'DataEntryController::dashboardData');
$routes->post('data/generate-elite-circle', 'DataEntryController::generateEliteCircle');
$routes->post('data/delete-elite-circle', 'DataEntryController::deleteEliteCircle');
$routes->get('data/ec-list', 'DataEntryController::ecList');
$routes->post('data/ec-create', 'DataEntryController::ecCreate');
$routes->match(['PUT', 'POST'], 'data/ec-update/(:any)', 'DataEntryController::ecUpdate/$1');
$routes->delete('data/ec-delete/(:any)', 'DataEntryController::ecDelete/$1');
// Elite Circle Records (manual data entry) CRUD
$routes->get('data/ec-records-list', 'DataEntryController::ecRecordsList');
$routes->post('data/ec-records-create', 'DataEntryController::ecRecordsCreate');
$routes->put('data/ec-records-update/(:any)', 'DataEntryController::ecRecordsUpdate/$1');
$routes->delete('data/ec-records-delete/(:any)', 'DataEntryController::ecRecordsDelete/$1');
// Elite Circle Data CRUD routes (formerly steel_deck)
$routes->get('data/ec-data-list', 'DataEntryController::sdList');
$routes->post('data/ec-data-create', 'DataEntryController::sdCreate');
$routes->match(['PUT', 'POST'], 'data/ec-data-update/(:any)', 'DataEntryController::sdUpdate/$1');
$routes->delete('data/ec-data-delete/(:any)', 'DataEntryController::sdDelete/$1');

// Excel export routes
$routes->get('data/export/(:any)', 'DataEntryController::exportExcel/$1');

// Leaderboard API routes (no auth filter - accessible from JS fetch)
$routes->get('leaderboard/api/se', 'LeaderboardController::grandSlamSeData');
$routes->get('leaderboard/api/tb', 'LeaderboardController::grandSlamTbData');
$routes->get('leaderboard/api/ec', 'LeaderboardController::grandSlamEcData');
$routes->get('leaderboard/api/ec-monthly', 'LeaderboardController::eliteCircleMonthlyRanking');
$routes->get('leaderboard/api/years', 'LeaderboardController::availableYears');
$routes->post('leaderboard/api/save', 'LeaderboardController::saveLeaderboard');
$routes->get('leaderboard/api/leaderboard-data', 'LeaderboardController::getLeaderboardData');

// Executive Dashboard API routes (no auth filter - accessible from JS fetch)
$routes->get('executive/api/all-data', 'ExecutiveController::allData');
$routes->get('executive/api/kpi-summary', 'ExecutiveController::kpiSummary');
$routes->get('executive/api/performance-trend', 'ExecutiveController::performanceTrend');
$routes->get('executive/api/dashboard-comparison', 'ExecutiveController::dashboardComparison');
$routes->get('executive/api/regional-performance', 'ExecutiveController::regionalPerformance');
$routes->get('executive/api/top-performers', 'ExecutiveController::topPerformers');
$routes->get('executive/api/achievement-distribution', 'ExecutiveController::achievementDistribution');
$routes->get('executive/api/recent-achievements', 'ExecutiveController::recentAchievements');
$routes->get('executive/api/employee-search', 'ExecutiveController::employeeSearch');
$routes->get('executive/api/participant-names', 'ExecutiveController::participantNames');
$routes->get('executive/api/employee-profile', 'ExecutiveController::employeeProfile');
$routes->get('executive/api/participant-profile', 'ExecutiveController::participantProfile');
$routes->get('executive/api/available-months', 'ExecutiveController::availableMonths');
$routes->get('executive/export', 'ExecutiveController::exportData');

// Protected Routes
$routes->group('', ['filter' => 'auth'], static function ($routes) {
    $routes->get('dashboard', 'DashboardController::index');

    // Dashboard analytics API endpoints
    $routes->get('dashboard/api/kpi-summary', 'DashboardController::kpiSummary');
    $routes->get('dashboard/api/monthly-sales-trend', 'DashboardController::monthlySalesTrend');
    $routes->get('dashboard/api/regional-performance', 'DashboardController::regionalPerformance');
    $routes->get('dashboard/api/top-dealers', 'DashboardController::topDealers');
    $routes->get('dashboard/api/award-distribution', 'DashboardController::awardDistribution');
    $routes->get('dashboard/api/smart-insights', 'DashboardController::smartInsights');
    $routes->get('dashboard/api/regional-data', 'DashboardController::regionalData');
    $routes->get('dashboard/api/top-awardees', 'DashboardController::topAwardees');
    $routes->get('dashboard/api/awardee-history', 'DashboardController::awardeeHistory');
    $routes->get('dashboard/api/all-data', 'DashboardController::allData');
    $routes->get('dashboard/api/filters/regions', 'DashboardController::filterRegions');
    $routes->get('dashboard/api/filters/dealers', 'DashboardController::filterDealers');
    $routes->get('dashboard/api/filters/award-statuses', 'DashboardController::filterAwardStatuses');
    $routes->get('dashboard/api/filters/grand-slam-statuses', 'DashboardController::filterGrandSlamStatuses');
    $routes->get('dashboard/api/current-dataset', 'DashboardController::currentDataset');
    $routes->post('dashboard/api/delete-dataset', 'DashboardController::deleteDataset');

    // Data Entry page (protected)
    $routes->get('data-entry', 'DataEntryController::index');

    // Leaderboard Dashboard page (protected)
    $routes->get('leaderboard', 'LeaderboardController::index');

    // Module skeleton routes (Scope A architecture pages)
    $routes->get('dealers', 'ModuleController::dealers');
    $routes->get('stores', 'ModuleController::stores');
    $routes->get('regions', 'ModuleController::regions');
    $routes->get('sales', 'ModuleController::sales');
    $routes->get('awardees', 'ModuleController::awardees');
    $routes->get('reports', 'ModuleController::reports');
    $routes->get('settings', 'SettingsController::index');

    // Analytics dashboard route - redirected to Executive Dashboard
    $routes->get('analytics', 'ExecutiveController::index');

    // Executive Dashboard main route (protected)
    $routes->get('executive', 'ExecutiveController::index');
    
    // Settings routes
    $routes->post('settings/update-profile', 'SettingsController::updateProfile');
    $routes->post('settings/upload-profile-pic', 'SettingsController::uploadProfilePic');
    $routes->post('settings/register-admin', 'SettingsController::registerAdmin');
    $routes->get('settings/deactivate-admin/(:num)', 'SettingsController::deactivateAdmin/$1');
    $routes->get('settings/activate-admin/(:num)', 'SettingsController::activateAdmin/$1');

    // Admin-only route group (expand in next modules)
    // $routes->group('admin', ['filter' => 'role:admin'], static function ($routes) {
    //     $routes->get('users', 'Admin\UserController::index');
    // });

    // Management-only route group (expand in next modules)
    // $routes->group('management', ['filter' => 'role:management'], static function ($routes) {
    //     $routes->get('reports', 'Management\ReportController::index');
    // });
});

if (is_file(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
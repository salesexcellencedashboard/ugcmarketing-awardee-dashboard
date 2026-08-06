<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    /**
     * Aliases for reusable filters.
     *
     * @var array<string, class-string|list<class-string>>
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'auth'          => \App\Filters\AuthFilter::class,
        'role'          => \App\Filters\RoleFilter::class,
    ];

    /**
     * Required filters are always applied before and after all other filters.
     *
     * @var array<string, list<string>>
     */
    public array $required = [
        'before' => [],
        'after'  => [],
    ];

    /**
     * Global filters.
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $globals = [
        'before' => [
            'csrf' => [
                'except' => [
                    // Auth routes
                    'login',
                    'index.php/login',
                    'forgot-password',
                    'index.php/forgot-password',

                    // Data Entry CRUD API routes
                    'data/se-list',
                    'data/se-create',
                    'data/se-update/*',
                    'data/se-delete/*',
                    'data/tb-list',
                    'data/tb-create',
                    'data/tb-update/*',
                    'data/tb-delete/*',
                    'data/ec-list',
                    'data/ec-create',
                    'data/ec-update/*',
                    'data/ec-delete/*',
                    'data/ec-records-list',
                    'data/ec-records-create',
                    'data/ec-records-update/*',
                    'data/ec-records-delete/*',
                    'data/dashboard',
                    'data/generate-elite-circle',
                    'data/delete-elite-circle',
                    'data/export/*',
                    'data/ec-data-list',
                    'data/ec-data-create',
                    'data/ec-data-update/*',
                    'data/ec-data-delete/*',
                    'data/clear-all',
                    'data/clear-type/*',
                    'data/upload-entry-photo',
                    'data/get-entry-photo/*',

                    // Leaderboard API routes
                    'leaderboard/api/se',
                    'leaderboard/api/tb',
                    'leaderboard/api/ec',
                    'leaderboard/api/ec-monthly',
                    'leaderboard/api/years',
                    'leaderboard/api/save',
                    'leaderboard/api/leaderboard-data',

                    // Executive Dashboard API routes
                    'executive/api/all-data',
                    'executive/api/kpi-summary',
                    'executive/api/performance-trend',
                    'executive/api/dashboard-comparison',
                    'executive/api/regional-performance',
                    'executive/api/top-performers',
                    'executive/api/achievement-distribution',
                    'executive/api/recent-achievements',
                    'executive/api/employee-search',
                    'executive/api/employee-profile',
                    'executive/api/participant-profile',
                    'executive/api/participant-names',
                    'executive/api/available-months',
                    'executive/export',
                ],
            ],
        ],
        'after' => [],
    ];

    /**
     * Method-specific filters.
     *
     * @var array<string, list<string>>
     */
    public array $methods = [];

    /**
     * URI pattern based filters.
     *
     * @var array<string, array<string, list<string>>>
     */
    public array $filters = [];
}

<?php

namespace Config;

use App\Filters\LoginFilter;
use App\Filters\PermissionFilter;
use App\Filters\RoleFilter;
use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf' => CSRF::class,
        'toolbar' => DebugToolbar::class,
        'honeypot' => Honeypot::class,
        'invalidchars' => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'pagecache' => PageCache::class,
        'performance' => PerformanceMetrics::class,

        // Myth/Auth
        'login' => LoginFilter::class,
        'permission' => PermissionFilter::class,
        'role' => RoleFilter::class,
    ];

    public array $globals = [
        'before' => [
            // Tidak ada login filter
        ],
        'after' => [
            'toolbar',
        ],
    ];

    public array $methods = [];

    public array $filters = [];
}

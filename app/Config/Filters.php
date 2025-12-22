<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use App\Filters\JwtAuth;
use App\Filters\RoleCheck;
use App\Filters\Cors;
use App\Filters\RateLimiter;

class Filters extends BaseConfig
{
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        // Custom Filters
        'jwt'           => JwtAuth::class,
        'role'          => RoleCheck::class,
        'cors'          => Cors::class,
        'rateLimit'     => RateLimiter::class,
    ];

    public array $globals = [
        'before' => [
            // 'honeypot',
            // 'csrf', // Disabled globally because we use JWT for API. Web forms use JS Fetch with headers.
            'cors', // Handle Cross-Origin
        ],
        'after' => [
            'toolbar',
            // 'honeypot',
            'secureheaders',
        ],
    ];

    public array $methods = [];

    public array $filters = [
        'rateLimit' => ['before' => ['api/*']], // Throttle API requests
    ];
}
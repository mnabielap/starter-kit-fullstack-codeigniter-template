<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Auth extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * JWT Secret Key
     * --------------------------------------------------------------------------
     * Used to sign and verify tokens.
     */
    public string $jwtSecret = 'YOUR_SECRET_KEY_FROM_ENV_SHOULD_BE_HERE';

    /**
     * --------------------------------------------------------------------------
     * Token Expirations
     * --------------------------------------------------------------------------
     */
    public int $jwtAccessExpirationMinutes = 60;
    public int $jwtRefreshExpirationDays = 30;
    public int $jwtResetPasswordExpirationMinutes = 15;
    public int $jwtVerifyEmailExpirationMinutes = 60;

    /**
     * --------------------------------------------------------------------------
     * Token Types
     * --------------------------------------------------------------------------
     */
    public array $tokenTypes = [
        'ACCESS'          => 'access',
        'REFRESH'         => 'refresh',
        'RESET_PASSWORD'  => 'resetPassword',
        'VERIFY_EMAIL'    => 'verifyEmail',
    ];

    /**
     * --------------------------------------------------------------------------
     * Role Permissions
     * --------------------------------------------------------------------------
     * Defines which roles can access which permission scopes.
     */
    public array $roleRights = [
        'user'  => [],
        'admin' => ['getUsers', 'manageUsers'],
    ];

    public function __construct()
    {
        parent::__construct();
        // Load overrides from .env
        $this->jwtSecret = getenv('JWT_SECRET') ?: $this->jwtSecret;
        $this->jwtAccessExpirationMinutes = (int)(getenv('JWT_ACCESS_EXPIRATION_MINUTES') ?: $this->jwtAccessExpirationMinutes);
        $this->jwtRefreshExpirationDays = (int)(getenv('JWT_REFRESH_EXPIRATION_DAYS') ?: $this->jwtRefreshExpirationDays);
    }
}
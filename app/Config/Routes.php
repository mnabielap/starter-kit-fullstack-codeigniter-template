<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// --------------------------------------------------------------------
// WEB ROUTES (View Renderers)
// --------------------------------------------------------------------
// These routes only return HTML skeletons. Data is fetched via JS.
$routes->group('', ['namespace' => 'App\Controllers\Web'], function ($routes) {
    // Auth Pages
    $routes->get('login', 'Auth::loginView');
    $routes->get('register', 'Auth::registerView');
    $routes->get('forgot-password', 'Auth::forgotPasswordView');
    $routes->get('reset-password', 'Auth::resetPasswordView'); // Handles query param ?token=... via JS

    // Dashboard
    $routes->get('/', 'Dashboard::index');

    // User Management Pages
    $routes->get('users', 'Users::index');
    $routes->get('users/create', 'Users::createView');
    $routes->get('users/edit', 'Users::editView'); // Handles query param ?id=... via JS
});

// --------------------------------------------------------------------
// API ROUTES (JSON Data)
// --------------------------------------------------------------------
$routes->group('api/v1', ['namespace' => 'App\Controllers\Api\V1'], function ($routes) {
    
    // Auth Endpoints
    $routes->group('auth', function ($routes) {
        $routes->post('register', 'Auth::register');
        $routes->post('login', 'Auth::login');
        $routes->post('logout', 'Auth::logout');
        $routes->post('refresh-tokens', 'Auth::refreshTokens');
        $routes->post('forgot-password', 'Auth::forgotPassword');
        $routes->post('reset-password', 'Auth::resetPassword');
        
        // Protected Auth Actions
        $routes->post('send-verification-email', 'Auth::sendVerificationEmail', ['filter' => 'jwt']);
        $routes->post('verify-email', 'Auth::verifyEmail');
    });

    // User Management Endpoints (Protected)
    $routes->group('users', ['filter' => 'jwt'], function ($routes) {
        // Admin: Create & List All
        $routes->post('/', 'Users::create', ['filter' => 'role:manageUsers']);
        $routes->get('/', 'Users::index', ['filter' => 'role:getUsers']);
        
        // Specific User Operations
        $routes->get('(:segment)', 'Users::show/$1', ['filter' => 'role:getUsers']);
        $routes->patch('(:segment)', 'Users::update/$1', ['filter' => 'role:manageUsers']);
        $routes->delete('(:segment)', 'Users::delete/$1', ['filter' => 'role:manageUsers']);
    });

    // Documentation
    $routes->get('docs', 'Docs::index'); 
});
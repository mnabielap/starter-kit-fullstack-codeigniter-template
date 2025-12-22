<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class Auth extends BaseController
{
    public function loginView()
    {
        return view('auth/login', ['title' => 'Login']);
    }

    public function registerView()
    {
        return view('auth/register', ['title' => 'Register']);
    }

    public function forgotPasswordView()
    {
        return view('auth/forgot-password', ['title' => 'Forgot Password']);
    }

    public function resetPasswordView()
    {
        return view('auth/reset-password', ['title' => 'Reset Password']);
    }
}
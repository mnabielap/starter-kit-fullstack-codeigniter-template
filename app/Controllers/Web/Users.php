<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class Users extends BaseController
{
    public function index()
    {
        return view('users/index', [
            'title'     => 'User Management',
            'pagetitle' => 'Users'
        ]);
    }

    public function createView()
    {
        return view('users/create', [
            'title'     => 'Create User',
            'pagetitle' => 'Users'
        ]);
    }

    public function editView()
    {
        return view('users/edit', [
            'title'     => 'Edit User',
            'pagetitle' => 'Users'
        ]);
    }
}
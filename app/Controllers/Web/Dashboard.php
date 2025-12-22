<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        return view('dashboard/index', [
            'title'     => 'Dashboard',
            'pagetitle' => 'Dashboard'
        ]);
    }
}
<?php

namespace App\Controllers\Web;

use App\Controllers\BaseController;

class Home extends BaseController
{
    public function index()
    {
        return redirect()->to('/dashboard');
    }
}
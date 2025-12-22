<?php

namespace App\Controllers\Api\V1;

use App\Controllers\BaseController;

class Docs extends BaseController
{
    public function index()
    {
        // This view will load the Swagger UI assets
        return view('swagger');
    }
}
<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\Weather\Service;

class BaseController extends Controller
{
    public $service;

    public function __construct(Service $service)
    {
        $this->service = $service;
    }
}

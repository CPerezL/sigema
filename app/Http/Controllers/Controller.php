<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    protected $oriente = 0;
    protected $valle = 0;
    protected $taller = 0;
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;
}

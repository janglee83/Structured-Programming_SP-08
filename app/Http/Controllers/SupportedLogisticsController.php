<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\SupportedLogisticsRepository;

class SupportedLogisticsController extends ApiController
{
    private $supportedLogisticsRepository;

    public function __construct(SupportedLogisticsRepository $supportedLogisticsRepository)
    {
        $this->supportedLogisticsRepository = $supportedLogisticsRepository;
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\CustomerInformationRepository;

class CustomerInformationController extends ApiController
{
    private $customerInformationRepository;

    public function __construct(CustomerInformationRepository $customerInformationRepository)
    {
        $this->customerInformationRepository = $customerInformationRepository;
    }
}

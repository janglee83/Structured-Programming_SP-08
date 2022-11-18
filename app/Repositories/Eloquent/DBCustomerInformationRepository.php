<?php

namespace App\Repositories\Eloquent;

use App\Models\CustomerInformation;
use App\Repositories\CustomerInformationRepository;
use App\Repositories\Eloquent\DBRepository;

class DBCustomerInformationRepository extends DBRepository implements CustomerInformationRepository
{
    function __construct(CustomerInformation $model)
    {
        parent::__construct($model);
    }
}

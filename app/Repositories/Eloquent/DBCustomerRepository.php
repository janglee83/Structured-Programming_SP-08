<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\CustomerRepository;
use App\Repositories\Eloquent\DBRepository;

class DBCustomerRepository extends DBRepository implements CustomerRepository
{
    function __construct(Customer $model)
    {
        parent::__construct($model);
    }
}

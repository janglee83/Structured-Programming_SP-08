<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\BaseRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\Eloquent\DBRepository;

class DBCustomerRepository extends BaseRepository implements CustomerRepository
{
//    function __construct()
//    {
//
//    }

    public function model()
    {
        return Customer::class;
    }
}

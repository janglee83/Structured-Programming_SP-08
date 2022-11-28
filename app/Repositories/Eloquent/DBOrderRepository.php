<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Repositories\Eloquent\DBRepository;

class DBOrderRepository extends DBRepository implements OrderRepository {
    function __construct(Order $model)
    {
        parent::__construct($model);
    }
}

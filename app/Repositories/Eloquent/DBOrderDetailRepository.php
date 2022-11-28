<?php

namespace App\Repositories\Eloquent;

use App\Models\OrderDetail;
use App\Repositories\Eloquent\DBRepository;
use App\Repositories\OrderDetailRepository;

class DBOrderDetailRepository extends DBRepository implements OrderDetailRepository {
    function __construct(OrderDetail $model)
    {
        parent::__construct($model);
    }
}

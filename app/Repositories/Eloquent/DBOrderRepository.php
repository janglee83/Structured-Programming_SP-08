<?php

namespace App\Repositories\Eloquent;

use App\Models\Order;
use App\Repositories\BaseRepository;
use App\Repositories\OrderRepository;
use App\Repositories\Eloquent\DBRepository;

class DBOrderRepository extends BaseRepository implements OrderRepository {
    public function model()
    {
        return Order::class;
    }

    public function getOrderData($filter) {
        return $this->model->with(["order_details", "order_details.product"])->select()
            ->when(isset($filter['date']), function ($query) use ($filter) {
                return $query->where('order_date', $filter['date']);
            })
            ->get();
    }

}

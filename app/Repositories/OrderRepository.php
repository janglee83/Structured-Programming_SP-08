<?php

namespace App\Repositories;

interface OrderRepository extends RepositoryInterface {
    public function getOrderData($filter);
}

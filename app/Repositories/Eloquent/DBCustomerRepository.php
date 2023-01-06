<?php

namespace App\Repositories\Eloquent;

use App\Models\Customer;
use App\Repositories\BaseRepository;
use App\Repositories\CustomerRepository;
use App\Repositories\Eloquent\DBRepository;

class DBCustomerRepository extends BaseRepository implements CustomerRepository
{
    public function model()
    {
        return Customer::class;
    }

    public function getUserData($filter) {
        return $this->model->select()
            ->when(isset($filter['user_id']), function ($query) use ($filter) {
                return $query->where('user_id', $filter['user_id']);
            })
            ->when(!empty($filter['name']) && strlen($filter['name']) >= 3, function ($query) use ($filter) {
                return $query->where('name', 'like', '%'. $filter['name'] .'%');
            })
            ->get();
    }
}

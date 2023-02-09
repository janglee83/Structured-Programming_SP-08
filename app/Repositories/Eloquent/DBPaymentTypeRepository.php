<?php

namespace App\Repositories\Eloquent;

use App\Models\PaymentType;
use App\Repositories\BaseRepository;
use App\Repositories\PaymentTypeRepository;

class DBPaymentTypeRepository extends BaseRepository implements PaymentTypeRepository {

    public function model()
    {
        return PaymentType::class;
    }

    public function getPaymentTypes()
    {
        return $this->model->all(['payment_type', 'status']);
    }
}

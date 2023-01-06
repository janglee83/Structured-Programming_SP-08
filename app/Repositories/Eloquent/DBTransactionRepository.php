<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\BaseRepository;
use App\Repositories\TransactionRepository;

class DBTransactionRepository extends BaseRepository implements TransactionRepository {
    public function model()
    {
        return Transaction::class;
    }

    public function findByPaymentCode($payment_code)
    {
        return $this->model->where('payment_code', $payment_code)->first();
    }
}

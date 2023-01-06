<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Repositories\Eloquent\DBRepository;

class DBTransactionRepository extends DBRepository implements TransactionRepository {
    function __construct(Transaction $model)
    {
        parent::__construct($model);
    }
}

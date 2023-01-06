<?php

namespace App\Repositories;

interface TransactionRepository extends RepositoryInterface {
    public function findByPaymentCode($payment_code);
}

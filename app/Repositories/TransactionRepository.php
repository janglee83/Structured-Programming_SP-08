<?php

namespace App\Repositories;

interface TransactionRepository extends RepositoryInterface {
    public function findByPaymentCode($payment_code);

    public function getTransactions($filter);

    public function getStatistic($filter);
}

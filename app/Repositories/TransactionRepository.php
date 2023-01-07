<?php

namespace App\Repositories;

interface TransactionRepository extends RepositoryInterface {
    public function findByPaymentCode($payment_code);

    public function getTransactions($filter, $limit = 10);

    public function getStatistic($filter);

    public function getStatisticByPaymentMethod($filter);
}

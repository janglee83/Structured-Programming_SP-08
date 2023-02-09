<?php

namespace App\Repositories;

interface PaymentTypeRepository extends RepositoryInterface {
    public function getPaymentTypes();

    public function setStatusPaymentTypeToNull();
}

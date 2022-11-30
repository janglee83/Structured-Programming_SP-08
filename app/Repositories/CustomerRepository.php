<?php

namespace App\Repositories;

interface CustomerRepository extends RepositoryInterface {
    public function getUserData($filter);
}

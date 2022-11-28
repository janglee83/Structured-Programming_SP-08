<?php

namespace App\Repositories\Eloquent;

class DBRepository {
    /**
     * Eloquent model
     */
    protected $model;

    /**
     * @param $model
     */
    protected function __construct($model)
    {
        $this->model = $model;
    }
}

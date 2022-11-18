<?php

namespace App\Repositories\Eloquent;

use App\Models\SupportedLogistics;
use App\Repositories\Eloquent\DBRepository;
use App\Repositories\SupportedLogisticsRepository;

class DBSupportedLogisticsRepository extends DBRepository implements SupportedLogisticsRepository {
    function __construct(SupportedLogistics $model)
    {
        parent::__construct($model);
    }
}

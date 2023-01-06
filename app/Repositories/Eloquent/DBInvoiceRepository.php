<?php

namespace App\Repositories\Eloquent;

use App\Models\Invoice;
use App\Repositories\BaseRepository;
use App\Repositories\InvoiceRepository;

class DBInvoiceRepository extends BaseRepository implements InvoiceRepository {
    public function model()
    {
        return Invoice::class;
    }

    public function getInvoices() {
        return $this->model->with(["order", "order.order_details", "order.order_details.product"])->select()
            ->get();
    }

}

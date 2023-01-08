<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\BaseRepository;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\DB;

class DBTransactionRepository extends BaseRepository implements TransactionRepository {
    public function model()
    {
        return Transaction::class;
    }

    public function findByPaymentCode($payment_code)
    {
        return $this->model->where('payment_code', $payment_code)->first();
    }

    public function findByOrderId($order_id)
    {
        return $this->model->where('order_id', $order_id)->get();
    }

    public function getTransactions($filter, $limit = 10)
    {
        return $this->model->select()
            ->when(isset($filter['created_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', $filter['created_at']);
            })
            ->when(isset($filter['payment_code']), function ($query) use ($filter) {
                return $query->where('payment_code', $filter['payment_code']);
            })
            ->when(isset($filter['status']), function ($query) use ($filter) {
                return $query->where('status', $filter['status']);
            })
            ->when(isset($filter['payment_date']), function ($query) use ($filter) {
                return $query->where('payment_date', $filter['payment_date']);
            })
            ->when(isset($filter['method']), function ($query) use ($filter) {
                return $query->where('method', $filter['method']);
            })
            ->when(isset($filter['type']), function ($query) use ($filter) {
                return $query->where('type', $filter['type']);
            })
            ->paginate($limit);
    }


    public function getStatistic($filter)
    {
        $success = $this->model->select()->where('status', 'success')
            ->when(isset($filter['start_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', ">=", $filter['start_at']);
            })
            ->when(isset($filter['end_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', "<=", $filter['end_at']);
            })
            ->count();
        $fail = $this->model->select()->where('status', '!=', 'success')
            ->when(isset($filter['start_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', ">=", $filter['start_at']);
            })
            ->when(isset($filter['end_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', "<=", $filter['end_at']);
            })
            ->count();
        $pay = $this->model->select()->where('type', 'pay')
            ->when(isset($filter['start_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', ">=", $filter['start_at']);
            })
            ->when(isset($filter['end_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', "<=", $filter['end_at']);
            })
            ->count();
        $refund = $this->model->select()->where('type', 'refund')
            ->when(isset($filter['start_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', ">=", $filter['start_at']);
            })
            ->when(isset($filter['end_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', "<=", $filter['end_at']);
            })
            ->count();

        return [
            'success' => $success,
            'fail' => $fail,
            'pay' => $pay,
            'refund' => $refund
        ];
    }

    public function getStatisticByPaymentMethod($filter)
    {
        return $this->model
            ->when(isset($filter['start_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', ">=", $filter['start_at']);
            })
            ->when(isset($filter['end_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', "<=", $filter['end_at']);
            })
            ->get()
            ->countBy(function ($item) {
                return $item['method'];
            });
    }
}

<?php

namespace App\Repositories\Eloquent;

use App\Models\Transaction;
use App\Repositories\BaseRepository;
use App\Repositories\TransactionRepository;

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
        $success = $this->model->select()->where('status', 'successful')
            ->when(isset($filter['start_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', ">=", $filter['start_at']);
            })
            ->when(isset($filter['end_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', "<=", $filter['end_at']);
            })
            ->count();
        $fail = $this->model->select()->where('status', '=', 'failed')
            ->when(isset($filter['start_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', ">=", $filter['start_at']);
            })
            ->when(isset($filter['end_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', "<=", $filter['end_at']);
            })
            ->count();
        $pending = $this->model->select()->where('status', '=', 'pending')
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
            'successful' => $success,
            'failed' => $fail,
            'pending' => $pending,
            'pay' => $pay,
            'refund' => $refund
        ];
    }

    public function getStatisticByPaymentMethod($filter)
    {
        return $this->model
            ->where("type", "pay")
            ->where("status", "successful")
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

    public function getStatisticByMonth($filter)
    {
        // Group the transactions by month and status
        return $this->model
            ->when(isset($filter['start_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', ">=", $filter['start_at']);
            })
            ->when(isset($filter['end_at']), function ($query) use ($filter) {
                return $query->whereDate('created_at', "<=", $filter['end_at']);
            })
            ->selectRaw("DATE_FORMAT(created_at, '%Y-%m') as month, status, count(*) as count")
            ->groupBy('month', 'status')
            ->get();
    }

    public function getStatisticPayAndRefund($filter)
    {
        // Calculate the total revenue for the past month
        $totalRevenue = $this->model->where('type', 'pay')
            ->where("status", "successful")
            ->where('created_at', '>=', $filter['start_at'])
            ->where('created_at', '<=', $filter['end_at'])
            ->sum('money');

        // Calculate the total cashback for the past month
        $totalCashback = $this->model->where('type', 'refund')
            ->where("status", "successful")
            ->where('created_at', '>=', $filter['start_at'])
            ->where('created_at', '<=', $filter['end_at'])
            ->sum('money');

        // Group the transactions by day and type
        $transactionsByDay = $this->model
            ->where("status", "successful")
            ->where('created_at', '>=', $filter['start_at'])
            ->where('created_at', '<=', $filter['end_at'])
            ->selectRaw("DATE_FORMAT(created_at, '%m-%d') as day, type, sum(money) as total")
            ->groupBy('day', 'type')
            ->get();

        return [
            'total_revenue' => $totalRevenue,
            'total_cashback' => $totalCashback,
            'transactions' => $transactionsByDay
        ];
    }
}

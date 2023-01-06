<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    /**
     * The table associated with the model
     * @var string
     */
    protected $table = 'invoices';

    /**
     * The attributes that are mass assignanle
     *
     * @var array
     */
    protected $fillable = ['order_id', 'transaction_id', 'invoice_code', 'total', 'status', 'note', 'paid_at', 'canceled_at'];

    public static function generateCode($id)
    {
        return date('ymd') . sprintf("%06d", $id);
    }

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transaction::class, 'transaction_id');
    }
}

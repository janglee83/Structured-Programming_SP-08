<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    /**
     * The table associated with the model
     * @var string
     */
    protected $table = 'transactions';

    /**
     * The attributes that are mass assignanle
     *
     * @var array
     */
    protected $fillable = ['order_id', 'customer_id', 'method', 'payment_code', 'money', 'status', 'payment_date', 'type', 'bank_code'];

    public static function generateCode($id, $created_at)
    {
        return 'SP' . date('ymd', strtotime($created_at)) . sprintf("%04d", $id) ;
    }
}

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
    protected $fillable = ['order_id', 'user_id', 'method', 'customer_name', 'code', 'money', 'status', 'payment_date'];
}

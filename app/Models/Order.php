<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The table associated with the model
     * @var string
     */
    protected $table = 'orders';

    /**
     * The attributes that are mass assignanle
     *
     * @var array
     */
    protected $fillable = ['user_id', 'order_date'];

    public function order_details()
    {
        return $this->hasMany(OrderDetail::class, 'order_id');
    }
}

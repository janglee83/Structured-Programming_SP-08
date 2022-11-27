<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    use HasFactory;

    /**
     * The table associated with the model
     * @var string
     */
    protected $table = 'order_details';

    /**
     * The attributes that are mass assignanle
     *
     * @var array
     */
    protected $fillable = ['order_id', 'price', 'price_discount'];
}

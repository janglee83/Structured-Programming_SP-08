<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    /**
     * The table associated with the model
     * @var string
     */
    protected $table = 'products';

    /**
     * The attributes that are mass assignanle
     *
     * @var array
     */
    protected $fillable = ['name', 'color', 'size', 'price', 'price_discount'];

}

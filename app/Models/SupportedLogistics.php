<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupportedLogistics extends Model
{
    use HasFactory;

    /**
     * The table associated with the model
     *
     * @var string
    */
    protected $table = 'supported_logistics';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description'];
}

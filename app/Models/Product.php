<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Product extends Model {

    protected $fillable = [
        'EAN', 'title', 'list_price'
    ];

}
<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Order extends Model {

    protected $fillable = [
        'customer_id', 'country', 'device', 'created_at'
    ];

    public function items()
    {   
        return $this->hasMany('App\Models\OrderItem');
    }

}
<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model {

    public $timestamps = false;

    protected $fillable = [
        'order_id', 'product_id', 'quantity', 'unit_price_paid'
    ];

    public function product()
    {   
        return $this->belongsTo('App\Models\Product')->select([
            'id',
            'title'
        ]);
    }

}
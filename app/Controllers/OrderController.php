<?php 

namespace App\Controllers;

use App\Responses\OrderResponse;
use App\Models\Order;
use App\Models\OrderItem;
use Carbon\Carbon;

class OrderController {

    public function index() {

        $date_start = $_GET['date_start'].' 00:00:01';
        $date_end = $_GET['date_end'].' 00:00:01';

        $orders = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('created_at', '>=', $date_start)
            ->where('created_at', '<=', $date_end)
            ->select('*','order_items.id as id', 'orders.id as order_id')
            ->orderBy('created_at', 'asc')
            ->get();

        $response = [];

        foreach($orders as $order){
            $order_response = [
                'id' => $order->id,
                'order_id' => $order->order_id,
                'device' => $order->device,
                'quantity' => $order->quantity,
                'order_items_price' => $order->quantity * $order->unit_price_paid,
                'product_id' => $order->product_id,
                'product_title' => $order->product->title,
                'created_at' => date('Y-m-d' , strtotime($order->created_at))
            ];
            array_push($response, $order_response);
        }
    
        echo(json_encode($response));
    }

}
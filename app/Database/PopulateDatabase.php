<?php

namespace App\Database;

use Faker\Factory;
use App\Models\Customer;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;

class PopulateDatabase {

    public function __construct() {

        $this->faker = Factory::create();

        $this->create_customers();
    }

    public function create_customers() {

        if (!Customer::exists()) {

            for($i = 0; $i < 100; $i++) {

                $created_at = $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null);
            
                # Error when importing the specific date value from faker to MySQL
                while($created_at->format("Y-m-d") == "2021-03-28" ){
                    $created_at = $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null);
                }

                Customer::create([
                    'first_name' => $this->faker->firstName,
                    'last_name' => $this->faker->lastName,
                    'email' => $this->faker->email,
                    'created_at' => $created_at
                ]);
            }
        }

        $this->create_products();
    }

    public function create_products() {
        
        if (!Product::exists()) {

            $base_price = 50;

            for($i=1; $i < 16; $i++) {
                Product::create([
                    'EAN' => $this->faker->ean13,
                    'title' => 'Product '.$i,
                    'list_price' => $base_price*$i
                ]);
            }
        }

        $this->create_orders();
    }

    public function create_orders() {
        
        if (!Order::exists()) {

            $customer = Customer::get('id');
            $device = ['Phone', 'Desktop', 'Pad'];

            for($i = 0; $i < 5000; $i++) {

            $created_at = $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null);
            
            # Error when importing the specific date value from faker to MySQL
            while($created_at->format("Y-m-d") == "2021-03-28" ){
                $created_at = $this->faker->dateTimeBetween($startDate = '-1 years', $endDate = 'now', $timezone = null);
            }
                
                Order::create([
                    'customer_id' => $customer[rand(0, sizeof($customer)-1)]->id,
                    'country' => $this->faker->country,
                    'device' => $device[rand(0, 2)],
                    'created_at' => $created_at
                ]);
            }
        }

        $this->create_order_items();

    }

    public function create_order_items() {

        if (!OrderItem::exists()) {
            
            $orders = Order::get('id');
            $products = Product::get();
            $count = 0;
            foreach ($orders as $order) {
                for($i = 0; $i < rand(1,3); $i++) {

                    # Loop through products
                    $product = $products[$count % sizeof($products)];

                    $quantity = rand(1,3);

                    OrderItem::create([
                        'order_id' => $order->id,
                        'product_id' => $product->id,
                        'quantity' => $quantity,
                        'unit_price_paid' => $product->list_price
                    ]);

                    $count++;
                }
            }
            echo('done');
        } else {
            echo('done');
        }
    }
    
}
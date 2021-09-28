<?php

namespace App\Router;

use App\Controllers\OrderController;
use App\Database\PopulateDatabase;
use App\Models\OrderItem;

class App {

    public function __construct($controller = 'order', $method = 'check'){

        if( $_GET['database'] == 'check' ){
            # Check if table order_items has rows.
            echo(OrderItem::exists());

        } else if( $_GET['database'] == 'populate' ) {
            $this->populate_database();
        } else {

            $controller = $_GET['controller'];
            $method = $_GET['method'];

            switch ($controller) {
                case 'order':
                    # code...
                    switch ($method) {
                        case 'index':
                            $data = OrderController::index();
                            break;
                        case 'dates':
                                # code...
                                break;
                        default:
                            # code...
                            break;
                    }

                    break;

                case 'customer':
                    # code...
                    break;

                default:
                    # code...
                    break;
            }
        }

    }

    public function populate_database() {
        new PopulateDatabase();
    }
}
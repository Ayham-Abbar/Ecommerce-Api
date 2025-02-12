<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Product;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\SellerPayment;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $total_users = User::count();
        $total_buyers = User::role('buyer')->count();
        $total_sellers = User::role('seller')->count();
        $total_products = Product::count();
        $total_orders = Order::count();
        $completed_orders = Order::where('status', 'completed')->count();
        $pending_orders = Order::where('status', 'pending')->count();
        $total_payments = Payment::sum('amount');
        $pending_payments = SellerPayment::where('status', 'pending')->sum('amount');
        $paid_payments = SellerPayment::where('status', 'paid')->sum('amount');
        $total_payments = $pending_payments + $paid_payments;
        $total_products_value = Product::sum('price');
        $max_product_value = Product::max('price');
        $min_product_value = Product::min('price');
        $most_expensive_product = Product::orderBy('price', 'desc')->first();
        $cheapest_product = Product::orderBy('price', 'asc')->first();
        $most_sold_product = OrderItem::select('product_id', DB::raw('SUM(quantity) as total_sold'))
        ->groupBy('product_id')
        ->orderByDesc('total_sold')
        ->with('product')
        ->first();
            $total_products_value_in_usd = $total_products_value * 3.5;
        return response()->json(['total_users' => $total_users,
                                  'total_buyers' => $total_buyers,
                                  'total_sellers' => $total_sellers,
                                  'total_products' => $total_products,
                                  'total_orders' => $total_orders,
                                  'completed_orders' => $completed_orders,
                                  'pending_orders' => $pending_orders,
                                  'total_payments' => $total_payments,
                                  'pending_payments' => $pending_payments,
                                  'paid_payments' => $paid_payments,
                                  'total_products_value' => $total_products_value,
                                  'total_products_value_in_usd' => $total_products_value_in_usd,
                                  'max_product_value' => $max_product_value,
                                  'min_product_value' => $min_product_value,
                                  'most_expensive_product' => $most_expensive_product,
                                  'cheapest_product' => $cheapest_product,
                                  'most_sold_product' => $most_sold_product]);
    }
}

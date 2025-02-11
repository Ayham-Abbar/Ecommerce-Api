<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BuyerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $orders = $user->orders;
        $products = $orders->pluck('products')->flatten();
        $total_orders = $orders->count();
        $total_products = $products->count();
        $total_payments = $user->payments->sum('amount');
        $favorite_products = $user->favorite_products;
        
        return response()->json(['orders' => $orders,
                                   'products' => $products,
                                   'total_orders' => $total_orders,
                                    'total_products' => $total_products,
                                     'total_payments' => $total_payments,
                                     'favorite_products' => $favorite_products]);
    }
}

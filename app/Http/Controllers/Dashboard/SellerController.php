<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SellerController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();
        $products = $user->products;
        $total_products = $products->count();
        // $total_orders = $user->orders->count();
        // $total_payments = $user->payments->sum('amount');
        // $favorite_products = $user->favorite_products;
        return response()->json(['products' => $products,
                                  'total_products' => $total_products]);
    }
}

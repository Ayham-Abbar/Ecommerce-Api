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
        $total_payments = $user->seller_payments->sum('amount');
        $pending_payments = $user->seller_payments->where('status', 'pending')->sum('amount');
        $paid_payments = $user->seller_payments->where('status', 'paid')->sum('amount');
        return response()->json(['products' => $products,
                                  'total_products' => $total_products,
                                  'total_payments' => $total_payments,
                                  'pending_payments' => $pending_payments,
                                  'paid_payments' => $paid_payments]);
    }
}

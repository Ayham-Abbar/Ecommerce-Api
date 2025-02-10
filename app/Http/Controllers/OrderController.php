<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{
    public function checkout()
    {
        $cartItems = Cart::where('buyer_id', Auth::id())->with('product')->get();

        if ($cartItems->isEmpty()) {
            return response()->json(['message' => 'Cart is empty'], 400);
        }

        // حساب إجمالي السعر
        $totalPrice = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);


        // إنشاء الطلب الرئيسي
        $order = Order::create([
            'buyer_id' => Auth::id(),
            'total_price' => $totalPrice,
            'status' => 'pending'
        ]);

        // إضافة تفاصيل المنتجات في جدول `order_items`
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product->id,
                'quantity' => $item->quantity,
                'price_at_purchase' => $item->product->price, // حفظ السعر عند وقت الشراء
            ]);
        }

        // إفراغ السلة بعد تأكيد الطلب
        Cart::where('buyer_id', Auth::id())->delete();

        return response()->json([
            'message' => 'Order placed successfully',
            'order' => $order->load('items.product') // تحميل تفاصيل المنتجات مع الطلب
        ], 201);
    }
    
    public function getOrders()
    {
        $orders = Order::where('buyer_id', Auth::id())->with('items.product')->get();
        return response()->json($orders, 200);
    }
}

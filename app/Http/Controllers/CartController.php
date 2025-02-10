<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    // 🛒 إضافة منتج إلى السلة
    public function addToCart(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'integer|min:1'
        ]);

        $cartItem = Cart::where('buyer_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($cartItem) {
            // تحديث الكمية إذا كان المنتج موجودًا بالفعل في السلة
            $cartItem->update(['quantity' => $cartItem->quantity + ($request->quantity ?? 1)]);
        } else {
            // إنشاء عنصر جديد في السلة
            Cart::create([
                'buyer_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity ?? 1
            ]);
        }

        return response()->json(['message' => 'Product added to cart successfully'], 201);
    }
    // 🔄 تحديث كمية منتج في السلة
    public function updateCart(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        $cartItem = Cart::where('buyer_id', Auth::id())->findOrFail($id);
        $cartItem->update(['quantity' => $request->quantity]);

        return response()->json(['message' => 'Cart updated successfully'], 200);
    }
    // ❌ حذف منتج من السلة
    public function removeFromCart($id)
    {
        $cartItem = Cart::where('buyer_id', Auth::id())->findOrFail($id);
        $cartItem->delete();

        return response()->json(['message' => 'Product removed from cart'], 200);
    }
      // 📋 عرض السلة الخاصة بالمستخدم
      public function getCart()
      {
        $cartItems = Cart::where('buyer_id', Auth::id())->with('product')->get();
        return response()->json($cartItems, 200);
    }   
    // 🧹 مسح السلة الخاصة بالمستخدم
    public function clearCart()
    {
        Cart::where('buyer_id', Auth::id())->delete();
        return response()->json(['message' => 'Cart cleared successfully'], 200);
    }
}

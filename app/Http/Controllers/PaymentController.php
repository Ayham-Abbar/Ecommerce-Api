<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Stripe\Stripe;
use Stripe\Charge;

class PaymentController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'token' => 'required'
        ]);

        //يستخدم للربط بين الموقع والحساب الخاص بالموقع في الموقع الخاص بسترايب
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        try {
            //يستخدم لإنشاء المعلومات المطلوبة للدفع
            $charge = Charge::create([
                'amount' => $request->amount * 100, // Stripe يستخدم السنتات
                'currency' => 'usd',
                'source' => $request->token,
                'description' => 'Order Payment'
            ]);

            //يعيد الرد للمستخدم بنجاح
            return response()->json(['message' => 'Payment successful', 'charge' => $charge], 200);
        } catch (\Exception $e) {
            //يعيد الرد للمستخدم بخطأ
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}

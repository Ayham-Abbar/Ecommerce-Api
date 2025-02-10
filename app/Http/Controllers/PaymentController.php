<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\User;
use App\Notifications\PaymentSuccessful;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
            $payment = Payment::create([
                'buyer_id' => Auth::user()->id, // المستخدم الذي قام بالدفع
                'payment_id' => $charge->id, // معرف الدفع من Stripe
                'amount' => $charge->amount / 100, // تحويل من سنتات إلى دولار
                'currency' => $charge->currency,
                'payment_status' => $charge->status,
                'payment_details' => json_encode($charge), // تخزين تفاصيل الدفع
            ]);
            
            $user = User::find(Auth::user()->id);
            $user->notify(new PaymentSuccessful($payment));
            //يعيد الرد للمستخدم بنجاح
            return response()->json(['message' => 'Payment successful', 'charge' => $charge], 200);
        } catch (\Exception $e) {
            //يعيد الرد للمستخدم بخطأ
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
    public function getPayments()
    {
        $payments = Payment::where('buyer_id', Auth::user()->id)->get();
        return response()->json($payments);
    }
}

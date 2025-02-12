<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\SellerPayment;
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
            'token' => 'required'
        ]);

        $user = Auth::user();
        $order = Order::where('buyer_id', $user->id)->where('status', 'pending')->firstOrFail();
        //يستخدم للربط بين الموقع والحساب الخاص بالموقع في الموقع الخاص بسترايب
        Stripe::setApiKey(env('STRIPE_SECRET_KEY'));

        try {
            //يستخدم لإنشاء المعلومات المطلوبة للدفع
            $charge = Charge::create([
                'amount' =>$order->total_price * 100, // Stripe يستخدم السنتات
                'currency' => 'usd',
                'source' => $request->token,
                'description' => 'Order Payment'
            ]);
            $payment = Payment::create([
                'buyer_id' => $user->id, // المستخدم الذي قام بالدفع
                'payment_id' => $charge->id, // معرف الدفع من Stripe
                'amount' => $order->total_price, // تحويل من سنتات إلى دولار
                'currency' => $charge->currency,
                'payment_status' => $charge->status,
                'payment_details' => json_encode($charge), // تخزين تفاصيل الدفع
            ]);

            // توزيع المبلغ على البائعين
            $orderItems = OrderItem::where('order_id', $order->id)->get();

            $seller_payments = [];
            foreach ($orderItems as $item) {
                $seller_payments[] = SellerPayment::create([
                    'seller_id' => $item->product->seller_id,
                    'order_id' => $order->id,
                    'amount' => $item->price_at_purchase * $item->quantity,
                    'status' => 'pending', // لم يتم الدفع للبائع بعد
                ]);
            }
            $order->status = 'completed';
            $order->save();
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

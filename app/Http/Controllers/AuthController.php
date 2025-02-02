<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // إنشاء المستخدم
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // تعيين الدور الافتراضي "buyer" باستخدام Spatie
        $user->assignRole('buyer');

        // إنشاء التوكن باستخدام JWTAuth
        $token = JWTAuth::fromUser($user);

        // الرد مع التوكن
        return response()->json([
            'message' => 'User registered successfully as buyer',
            'user' => $user,
            'token-info' => $this->respondWithToken($token)  // مدة صلاحية التوكن
        ], 201);
    }
    // تسجيل الدخول
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = JWTAuth::attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    // تسجيل الخروج
    public function logout()
    {
        // التحقق إذا كان المستخدم قد سجل الدخول وأرسل توكن
        try {
            // إبطال التوكن الحالي
            JWTAuth::invalidate(JWTAuth::getToken());

            // إرجاع استجابة تفيد بأنه تم تسجيل الخروج بنجاح
            return response()->json(['message' => 'User logged out successfully'], 200);
        } catch (\Exception $e) {
            // في حالة حدوث خطأ أثناء إبطال التوكن
            return response()->json(['error' => 'Failed to logout, please try again'], 500);
        }
    }
    // دالة تحديث التوكن
    public function refreshToken(Request $request)
    {
        try {
            // الحصول على التوكن القديم
            $token = JWTAuth::getToken();
            // التأكد من وجود التوكن
            if (!$token) {
                return response()->json(['error' => 'Token not provided'], 400);
            }

            // تحديث التوكن باستخدام التوكن المنعش
            $newToken = JWTAuth::refresh($token);

            // إرجاع التوكن الجديد
            return response()->json([
                'access_token' => $newToken,
                'token_type' => 'bearer',
                'expires_in' => JWTAuth::factory()->getTTL() * 60
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Could not refresh token'], 500);
        }
    }

    // // إرجاع بيانات المستخدم الحالي
    public function profile()
    {
        $user = JWTAuth::user();
        return response()->json($user);
    }

    // // إرسال التوكن عند تسجيل الدخول
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => JWTAuth::factory()->getTTL() * 60
        ]);
    }
    public function requestSellerUpgrade()
    {
        $user = JWTAuth::user();
        // التأكد من أن المستخدم ليس بالفعل بائعًا
        if ($user->hasRole('seller')) {
            return response()->json(['message' => 'You are already a seller'], 400);
        }

        // تحديث الدور إلى "seller"
        $user->syncRoles(['seller']);

        return response()->json(['message' => 'Your account has been upgraded to seller'], 200);
    }
}

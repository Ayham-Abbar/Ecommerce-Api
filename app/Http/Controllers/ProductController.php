<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Favorite;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth;

class ProductController extends Controller
{
    // عرض المنتجات مع التصفية حسب الفئة
    public function index(Request $request)
    {
        $query = Product::query();
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('name', $request->category);
            });
        }
        return response()->json($query->get(), 200);
    }
    // إضافة منتج جديد للبائع
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'price' => 'required|numeric',
            'category' => 'required|string'
        ]);


        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $user = JWTAuth::user();
        $category = Category::firstOrCreate(['name' => $request->category]);
        $product = Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $category->id,
            'seller_id' => $user->id
        ]);
        return response()->json($product, 201);
    }

    // تحديث منتج للبائع
    public function update(Request $request, $id)
    {
        // // التحقق من صحة البيانات المدخلة
        $validator = Validator::make($request->all(), [
            'name' => 'sometimes|string|max:255',
            'price' => 'sometimes|numeric',
            'category' => 'sometimes|string'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        // الحصول على المستخدم الحالي
        $user = JWTAuth::user();

        // البحث عن المنتج والتحقق من أنه يخص البائع الحالي
        $product = Product::where('id', $id)->where('seller_id', $user->id)->firstOrFail();

        // تحديث الفئة إذا تم إرسالها في الطلب
        if ($request->has('category')) {
            $category = Category::firstOrCreate(['name' => $request->category]);
            $product->category_id = $category->id;
        }

        // تحديث البيانات المطلوبة
        $product->update($request->only(['name', 'price']));

        // حفظ المنتج بعد التعديلات
        $product->save();

        return response()->json([
            'message' => 'Product updated successfully',
            'product' => $product
        ], 200);
    }


    // حذف منتج للبائع
    public function destroy($id)
    {
        $product = Product::where('id', $id)->where('seller_id', JWTAuth::user()->id)->firstOrFail();

        $product->delete();

        return response()->json(['message' => 'Product deleted successfully'], 200);
    }

    // إضافة منتج للمفضلة أو إزالته
    public function toggleFavorite($id)
    {
        $user = JWTAuth::user();
        $favorite = Favorite::where('buyer_id', $user->id)->where('product_id', $id)->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json(['message' => 'Removed from favorites'], 200);
        }

        Favorite::create(['buyer_id' => $user->id, 'product_id' => $id]);
        return response()->json(['message' => 'Added to favorites'], 201);
    }
}

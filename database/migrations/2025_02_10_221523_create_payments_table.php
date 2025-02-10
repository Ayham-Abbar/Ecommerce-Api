<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade'); // المستخدم الذي قام بالدفع
            $table->string('payment_id')->unique(); // معرف الدفع من Stripe
            $table->decimal('amount', 10, 2); // المبلغ المدفوع
            $table->string('currency')->default('USD'); // العملة
            $table->string('payment_status'); // حالة الدفع (مكتمل، فشل، معلّق)
            $table->json('payment_details')->nullable(); // تفاصيل الدفع الإضافية (مثل البطاقة)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};

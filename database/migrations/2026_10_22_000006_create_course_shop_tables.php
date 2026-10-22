<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::table('courses',fn(Blueprint $t)=>$t->boolean('is_for_sale')->default(false));
  Schema::create('cart_items',function(Blueprint $t){$t->id();$t->foreignId('user_id')->constrained()->cascadeOnDelete();$t->foreignId('course_id')->constrained()->restrictOnDelete();$t->timestamps();$t->unique(['user_id','course_id']);});
  Schema::create('orders',function(Blueprint $t){$t->id();$t->uuid('number')->unique();$t->foreignId('user_id')->constrained()->restrictOnDelete();$t->uuid('checkout_token')->unique();$t->string('buyer_name');$t->string('buyer_email');$t->unsignedBigInteger('total');$t->string('currency',3)->default('VND');$t->enum('status',['pending','paid'])->default('pending');$t->timestamp('paid_at')->nullable();$t->timestamps();$t->index(['user_id','status']);});
  Schema::create('order_items',function(Blueprint $t){$t->id();$t->foreignId('order_id')->constrained()->restrictOnDelete();$t->foreignId('course_id')->constrained()->restrictOnDelete();$t->string('course_name');$t->unsignedBigInteger('unit_price');$t->unsignedInteger('access_days');$t->timestamps();$t->unique(['order_id','course_id']);});
  Schema::create('payments',function(Blueprint $t){$t->id();$t->foreignId('order_id')->unique()->constrained()->restrictOnDelete();$t->string('reference',40)->unique();$t->enum('method',['demo'])->default('demo');$t->enum('status',['pending','paid'])->default('pending');$t->unsignedBigInteger('amount');$t->foreignId('confirmed_by')->nullable()->constrained('users')->restrictOnDelete();$t->timestamp('confirmed_at')->nullable();$t->timestamps();});
 }
 public function down(): void {Schema::dropIfExists('payments');Schema::dropIfExists('order_items');Schema::dropIfExists('orders');Schema::dropIfExists('cart_items');Schema::table('courses',fn(Blueprint $t)=>$t->dropColumn('is_for_sale'));}
};

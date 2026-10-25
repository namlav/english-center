<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\{DB,Schema};
return new class extends Migration {
 public function up():void {
  Schema::table('payments',fn(Blueprint $t)=>$t->string('method',30)->default('demo')->change());
  Schema::create('payment_qr_requests',function(Blueprint $t){
   $t->id();$t->foreignId('payment_id')->constrained()->restrictOnDelete();$t->string('reference',40)->unique();
   $t->json('bank_snapshot');$t->unsignedBigInteger('amount');$t->timestamp('reported_at')->nullable();$t->timestamps();
  });
  DB::table('site_settings')->insertOrIgnore(['key'=>'payment_bank','value'=>json_encode(['enabled'=>true,'bank_bin'=>'970422','bank_name'=>'MB Bank','account_number'=>'0389279885','account_name'=>'NGUYEN QUOC TRUNG']),'created_at'=>now(),'updated_at'=>now()]);
 }
 public function down():void {Schema::dropIfExists('payment_qr_requests');}
};

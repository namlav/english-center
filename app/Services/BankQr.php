<?php
namespace App\Services;
use App\Models\{Order,Payment,PaymentQrRequest,SiteSetting};
use Illuminate\Support\Facades\{DB,Schema};
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
class BankQr {
 public function settings():array {return Schema::hasTable('site_settings')?(SiteSetting::where('key','payment_bank')->value('value')??[]):[];}
 public function enabled():bool {return (bool)($this->settings()['enabled']??false);}
 public function validatedSettings():array {
  $b=$this->settings();
  if(!preg_match('/^\d{6}$/',$b['bank_bin']??'')||!preg_match('/^[A-Za-z0-9]{1,19}$/',$b['account_number']??'')||trim($b['account_name']??'')==='')throw ValidationException::withMessages(['payment'=>'Admin cần hoàn tất tên chủ tài khoản trong Cài đặt QR ngân hàng trước khi tạo đơn.']);
  return array_intersect_key($b,array_flip(['bank_bin','bank_name','account_number','account_name']));
 }
 public function create(Order $order):PaymentQrRequest {
  return DB::transaction(function()use($order){
   $order=Order::whereKey($order->id)->lockForUpdate()->firstOrFail();
   $payment=Payment::where('order_id',$order->id)->lockForUpdate()->firstOrFail();
   abort_unless($order->status==='pending'&&$payment->method==='bank_transfer',409);
   $previous=PaymentQrRequest::where('payment_id',$payment->id)->latest('id')->first();
   return PaymentQrRequest::create(['payment_id'=>$payment->id,'reference'=>'EC'.strtoupper(str_replace('-','',(string)Str::uuid())),'bank_snapshot'=>$previous?->bank_snapshot??$this->validatedSettings(),'amount'=>$order->total]);
  },5);
 }
}

<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class PaymentQrRequest extends Model {
 protected $fillable=['payment_id','reference','bank_snapshot','amount','reported_at'];
 protected function casts():array{return ['bank_snapshot'=>'array','amount'=>'integer','reported_at'=>'datetime'];}
 public function payment(){return $this->belongsTo(Payment::class);}
 public function imageUrl():string {
  $b=$this->bank_snapshot;
  return 'https://img.vietqr.io/image/'.$b['bank_bin'].'-'.$b['account_number'].'-qr_only.svg?'.http_build_query(['amount'=>$this->amount,'addInfo'=>$this->reference,'accountName'=>$b['account_name']],'','&',PHP_QUERY_RFC3986);
 }
}

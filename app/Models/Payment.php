<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Payment extends Model {
 protected $fillable=['order_id','reference','method','status','amount','confirmed_by','confirmed_at'];
 protected function casts():array{return ['amount'=>'integer','confirmed_at'=>'datetime'];}
 public function order(){return $this->belongsTo(Order::class);}
}

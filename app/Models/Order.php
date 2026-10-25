<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Order extends Model {
 protected $fillable=['number','user_id','enrollment_id','checkout_token','buyer_name','buyer_email','total','currency','status','paid_at'];
 protected function casts():array{return ['total'=>'integer','paid_at'=>'datetime'];}
 public function items(){return $this->hasMany(OrderItem::class);}
 public function payment(){return $this->hasOne(Payment::class);}
 public function user(){return $this->belongsTo(User::class);}
 public function enrollment(){return $this->belongsTo(Enrollment::class);}
}

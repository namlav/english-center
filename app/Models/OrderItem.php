<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class OrderItem extends Model {
 protected $fillable=['order_id','course_id','course_name','unit_price','access_days'];
 protected function casts():array{return ['unit_price'=>'integer','access_days'=>'integer'];}
 public function course(){return $this->belongsTo(Course::class);}
 public function order(){return $this->belongsTo(Order::class);}
}

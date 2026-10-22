<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class PublicTeacher extends Model
{
    protected $fillable = ['name','slug','bio','photo_path','provider','source_url','qualifications','position','is_active'];
    protected function casts(): array { return ['qualifications'=>'array','position'=>'integer','is_active'=>'boolean']; }
    public function scopeVisible(Builder $query): Builder { return $query->where('is_active', true)->orderBy('position')->orderBy('id'); }
}

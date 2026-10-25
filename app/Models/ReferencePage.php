<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ReferencePage extends Model
{
    protected $fillable = ['provider', 'source_key', 'source_url', 'content_type', 'title', 'summary', 'metadata', 'captured_at'];

    public function images(): HasMany
    {
        return $this->hasMany(ReferenceImage::class)->orderBy('position');
    }

    protected function casts(): array
    {
        return ['metadata' => 'array', 'captured_at' => 'datetime'];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReferenceImage extends Model
{
    protected $fillable = ['reference_page_id', 'asset_key', 'source_url', 'local_path', 'alt_text', 'role', 'position', 'mime_type', 'width', 'height', 'file_size', 'content_sha256'];

    public function referencePage(): BelongsTo
    {
        return $this->belongsTo(ReferencePage::class);
    }

    protected function casts(): array
    {
        return ['position' => 'integer', 'width' => 'integer', 'height' => 'integer', 'file_size' => 'integer'];
    }
}

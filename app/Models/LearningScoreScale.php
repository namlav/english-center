<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningScoreScale extends Model
{
    protected $fillable = [
        'code', 'label', 'min_score', 'max_score', 'score_step', 'is_active',
    ];

    protected $primaryKey = 'code';

    public $incrementing = false;

    protected $keyType = 'string';

    public function goalBounds(): array
    {
        return [
            'score_scale_code' => $this->code,
            'min_score' => $this->min_score,
            'max_score' => $this->max_score,
            'score_step' => $this->score_step,
        ];
    }

    protected function casts(): array
    {
        return [
            'min_score' => 'decimal:2',
            'max_score' => 'decimal:2',
            'score_step' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }
}

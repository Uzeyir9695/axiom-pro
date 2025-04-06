<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rate extends Model
{
    use HasUuids;
    protected $fillable = [
        'rate',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }
}

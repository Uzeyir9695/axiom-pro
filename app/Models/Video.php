<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Video extends Model
{
    use HasUuids;
    protected $fillable = [
        'title',
        'description',
        'video_url',
    ];

    public function rates(): HasMany
    {
        return $this->hasMany(Rate::class);
    }
}

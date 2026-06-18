<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Track extends Model
{
    protected $fillable = ['album_id', 'name', 'track_number', 'tune_id'];

    public $timestamps = false;

    public function album(): BelongsTo
    {
        return $this->belongsTo(Album::class);
    }
}

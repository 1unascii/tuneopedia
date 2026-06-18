<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Album extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'artist', 'cover_art', 'user_id'];

    public $timestamps = false;

    public function tracks(): HasMany
    {
        return $this->hasMany(Track::class)->orderBy('track_number');
    }
}

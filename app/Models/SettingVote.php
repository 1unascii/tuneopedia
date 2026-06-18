<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SettingVote extends Model
{
    use HasFactory;
    protected $fillable = ['user_id', 'setting_id', 'vote_value'];

    public $timestamps = false;

    public function setting(): BelongsTo
    {
        return $this->belongsTo(Setting::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

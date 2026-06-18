<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DiscussionThread extends Model
{
    use HasFactory;

    protected $fillable = ['tune_id', 'title', 'body', 'user_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function discussionReplies(): HasMany
    {
        return $this->hasMany(DiscussionReply::class, 'discussion_thread_id');
    }
}

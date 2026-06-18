<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DiscussionReply extends Model
{
    protected $fillable = ['discussion_thread_id', 'user_id', 'body'];

    public $timestamps = false;

    public function discussionThread(): BelongsTo
    {
        return $this->belongsTo(DiscussionThread::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Setting extends Model
{
    use HasFactory;
    protected $fillable = [
        'tune_id', 'user_id', 'name', 'time_signature', 'default_note_length',
        'key_signature', 'abc_transcription', 'notes', 'source', 'origin',
        'history', 'book', 'discography', 'transcription_credit', 'area',
        'parts', 'tempo', 'instrument_id', 'lyrics',
    ];

    public $timestamps = false;

    public function tune(): BelongsTo
    {
        return $this->belongsTo(Tune::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function votes(): HasMany
    {
        return $this->hasMany(SettingVote::class);
    }

    /**
     * Build the full ABC string with headers for abcjs rendering.
     */
    public function toAbc(): string
    {
        $abc = "X:1\n";
        $abc .= "T:{$this->name}\n";

        if ($this->time_signature) {
            $abc .= "M:{$this->time_signature}\n";
        }

        if ($this->default_note_length) {
            $abc .= "L:{$this->default_note_length}\n";
        }

        if ($this->key_signature) {
            $abc .= "K:{$this->key_signature}\n";
        }

        return $abc.$this->abc_transcription;
    }
}

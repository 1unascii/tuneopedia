<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Tune extends Model
{
    use HasFactory;
    protected $fillable = ['name', 'tune_type_id', 'composer_id', 'composer', 'origin', 'source'];

    public $timestamps = false;

    public function tuneType(): BelongsTo
    {
        return $this->belongsTo(TuneType::class);
    }

    public function settings(): HasMany
    {
        return $this->hasMany(Setting::class);
    }

    /**
     * Get the highest-voted setting for this tune.
     *
     * HasOne returns a single record (LIMIT 1). withSum adds a
     * votes_sum_vote_value subquery column that totals all vote_value
     * entries from setting_votes for each setting. orderByDesc sorts
     * by that sum so the LIMIT 1 picks the most-voted setting.
     */
    public function topSetting(): HasOne
    {
        return $this->hasOne(Setting::class)
            ->withSum('votes', 'vote_value')
            ->orderByDesc('votes_sum_vote_value');
    }

    public function favoritedBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'favorites');
    }

    /**
     * The collections this tune belongs to.
     * Many-to-many through collection_tunes pivot table.
     */
    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_tunes');
    }
}

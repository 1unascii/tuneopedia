<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * A collection is a curated group of tunes (e.g. a tunebook, session set list).
 *
 * Collections have a many-to-many relationship with tunes through the
 * collection_tunes pivot table. The pivot includes a 'position' column
 * for ordering tunes within a collection.
 *
 * Collections can be public (is_shared = true) or private (is_shared = false).
 */
class Collection extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'name', 'author', 'description',
        'publisher', 'published_date', 'cover_image', 'is_shared',
    ];

    /** Uses created_at only — no updated_at column in the table */
    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'is_shared' => 'boolean',
            'published_date' => 'date',
        ];
    }

    /** The user who created this collection */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * The tunes in this collection.
     *
     * Many-to-many through collection_tunes pivot table.
     * withPivot('position') makes the ordering position accessible.
     * orderByPivot('position') sorts tunes by their position in the collection.
     */
    public function tunes(): BelongsToMany
    {
        return $this->belongsToMany(Tune::class, 'collection_tunes')
            ->withPivot('position')
            ->orderByPivot('position');
    }
}

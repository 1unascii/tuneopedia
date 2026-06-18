<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Composer extends Model
{
    protected $fillable = ['name'];

    public $timestamps = false;

    public function tunes(): HasMany
    {
        return $this->hasMany(Tune::class);
    }
}

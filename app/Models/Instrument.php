<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instrument extends Model
{
    protected $fillable = ['name', 'midi_program'];

    public $timestamps = false;
}

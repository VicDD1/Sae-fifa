<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $table = 'vote';
    protected $primaryKey = 'id_vote';
    public $timestamps = false;

    protected $fillable = ['id_vote', 'id_theme'];
}


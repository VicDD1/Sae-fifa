<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Player extends Model
{
    protected $fillable = ['name', 'photo_url', 'club', 'nationality'];

    public function voteSystems()
    {
        return $this->belongsToMany(VoteSystem::class, 'player_vote_system');
    }
}


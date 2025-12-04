<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoteSystem extends Model
{
    protected $fillable = ['name', 'description'];

    public function players()
    {
        return $this->belongsToMany(Player::class, 'player_vote_system');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Joueur_theme extends Model
{
    use HasFactory;

    protected $table = 'joueur_theme';
    public $incrementing = false;
    public $timestamps = false;

    protected $fillable = [
        'id_joueur',
        'id_theme',
    ];

    /**
     * Relation vers le joueur
     */
    public function joueur()
    {
        return $this->belongsTo(Joueur::class, 'id_joueur', 'id_joueur');
    }

    /**
     * Relation vers le thème de vote
     */
    public function theme()
    {
        return $this->belongsTo(Theme_vote::class, 'id_theme', 'id_theme');
    }
}

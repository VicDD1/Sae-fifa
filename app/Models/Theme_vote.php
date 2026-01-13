<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme_vote extends Model
{
    use HasFactory;

    protected $table = 'theme_vote';
    protected $primaryKey = 'id_theme';
    public $timestamps = false;

    protected $fillable = [
        'nom_theme',
        'date_fin_vote',
    ];

    protected $casts = [
        'date_fin_vote' => 'date',
    ];

    /**
     * Relation : joueurs associés à ce thème de vote
     */
    public function joueurs()
    {
        return $this->belongsToMany(Joueur::class, 'joueur_theme', 'id_theme', 'id_joueur');
    }

    /**
     * Vérifie si le thème est votable (a des joueurs et n'est pas expiré)
     */
    public function isVotable(): bool
    {
        return $this->joueurs()->count() > 0 
            && ($this->date_fin_vote === null || $this->date_fin_vote >= now()->startOfDay());
    }

    /**
     * Vérifie si le thème est expiré
     */
    public function isExpired(): bool
    {
        return $this->date_fin_vote !== null && $this->date_fin_vote < now()->startOfDay();
    }
}

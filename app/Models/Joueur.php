<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Joueur extends Model
{
    use HasFactory;

    // Table correspondant au modèle
    protected $table = "joueur";

    // Clé primaire
    protected $primaryKey = "id_joueur";

    // Pas de timestamps dans ta table
    public $timestamps = false;

    // Colonnes modifiables
    protected $fillable = [
        'prenom',
        'nom',
        'date_naissance_joueur',
        'lieu_naissance_joueur',
        'pied_prefere',
        'club',
        'poids_joueur',
        'taille_joueur',
        'biographie_joueur',
        'first_selection',
        'nb_selection'
    ];

    /**
     * Relation : thèmes de vote auxquels ce joueur participe
     */
    public function themes()
    {
        return $this->belongsToMany(Theme_vote::class, 'joueur_theme', 'id_joueur', 'id_theme');
    }

    /**
     * Nom complet du joueur
     */
    public function getNomCompletAttribute(): string
    {
        return $this->prenom . ' ' . $this->nom;
    }
}

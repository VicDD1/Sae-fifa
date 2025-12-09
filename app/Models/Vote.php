<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    use HasFactory;

    // Table correspondant à ton modèle
    protected $table = "theme_vote";

    // Clé primaire de la table
    protected $primaryKey = "id_theme";

    // Pas de timestamps dans ta table
    public $timestamps = false;

    // Colonnes modifiables
    protected $fillable = [
        'nom_theme'
    ];
}

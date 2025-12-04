<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    protected $table = 'adresse';               // nom EXACT de la table
    protected $primaryKey = 'id_adresse';       // clé primaire
    public $timestamps = false;                 // pas de created_at / updated_at

    protected $fillable = [
        'pays_adresse',
        'code_postal',
        'ville_adresse',
    ];

    // Relation vers commande
    public function commandes()
    {
        return $this->hasMany(Commande::class, 'id_adresse', 'id_adresse');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Adresse extends Model
{
    protected $table = 'adresse';
    protected $primaryKey = 'id_adresse';
    public $timestamps = false;

    protected $fillable = [
        'pays_adresse',
        'code_postal',
        'ville_adresse',
        'latitude',
        'longitude',
    ];

    // Relation vers commande
    public function commandes()
    {
        return $this->hasMany(Commande::class, 'id_adresse', 'id_adresse');
    }

    // Adresse complète pour géocodage
    public function getFullAddressAttribute()
    {
        return "{$this->code_postal} {$this->ville_adresse}, {$this->pays_adresse}";
    }
}

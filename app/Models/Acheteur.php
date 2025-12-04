<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acheteur extends Model
{
    protected $table = 'acheteur';
    protected $primaryKey = 'id_acheteur';

    public $timestamps = false;
    public $incrementing = false;

    protected $fillable = [
        'id_acheteur',
        'prenom_user_connecte',
        'courriel_user_connecte',
        'date_de_naissance_user_connecte',
        'pays_de_naissance_user_connecte',
        'langue_user_connecte',
        'surnom_user_connecte',
        'favori_user_connecte',
        'telephone_acheteur',
        'adresse_livraison',
    ];
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Acheteur extends Model
{
    protected $table = 'acheteur';
    protected $primaryKey = 'id_acheteur';

    public $timestamps = false;

    protected $fillable = [
        'id_user_connecte',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'id_user_connecte', 'id_user_connecte');
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Panier extends Model
{
    protected $table = 'panier';
    protected $primaryKey = 'id_panier';

    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_user_connecte',
        'id_acheteur'
    ];

    public function lignes()
    {
        return $this->hasMany(Ligne_panier::class, 'id_panier', 'id_panier');
    }
}

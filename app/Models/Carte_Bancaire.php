<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Carte_Bancaire extends Model
{
    use HasFactory;

    protected $table = 'carte_bancaire';         
    protected $primaryKey = 'id_carte';   

    public $timestamps = false;

    protected $fillable = [
        'id_user_connecte',
        'numero_carte',
        'date_expiration',
        'cryptogramme',
        'nom_titulaire',
    ];
}

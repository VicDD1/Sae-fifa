<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Professionnel extends Model
{
    use HasFactory;
    protected $table = 'professionnel';
    protected $primaryKey = 'id_professionnel';
    public $timestamps = false; 
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
    'nom_societe',
    'numero_TVA',
    'activite_professionnel',
    'email_professionnel',
    'nom_professionnel',
    'prenom_professionnel',
    'adresse_professionnel',
    'code_postal_professionnel',
    'pays_professionnel',
    'ville_professionnel',
    'telephone_professionnel',
    'password_professionnel',
    'id_user_connecte',
];
public function user()
{
    return $this->belongsTo(UserConnecte::class, 'id_user_connecte');
}
}

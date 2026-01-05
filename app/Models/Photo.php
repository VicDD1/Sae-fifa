<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;

    protected $table = 'photo';       // Nom de la table (minuscule)
    protected $primaryKey = 'id_photo'; 
    public $timestamps = false;

    protected $fillable = [
        'code_photo',  // <--- C'est le nom de ta colonne sur ta capture d'écran
        'id_produit'
    ];
}
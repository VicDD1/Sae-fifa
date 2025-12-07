<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit_Propose extends Model
{
    use HasFactory;
    protected $table = 'demande_produits';
    protected $primaryKey = 'id_demande';
    public $timestamps = false; 
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
        'id_professionnel',
        'id_user_connecte',
        'nom_demande',
        'description',
        
    ];
}
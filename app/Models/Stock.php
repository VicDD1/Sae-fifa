<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    use HasFactory;

    protected $table = 'variante_produit'; 
    public $timestamps = false;
    
    // --- CHANGEMENT ICI ---
    // On définit la vraie clé primaire maintenant qu'elle est configurée en SQL
    protected $primaryKey = 'id_variante';
    public $incrementing = true; // On repasse à true car PostgreSQL va gérer l'ID
    // ----------------------

    protected $fillable = [
        'id_produit',
        'id_taille',
        'id_colori',
        'quantitee_stock' // Avec tes "ee" :)
    ];
}
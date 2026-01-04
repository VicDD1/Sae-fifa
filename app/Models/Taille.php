<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taille extends Model
{
    use HasFactory;
    protected $table = 'taille';
    protected $primaryKey = 'id_taille';
    public $timestamps = false; 
    protected $fillable = [


        'label_taille',
    ];
public function produits()
{
    return $this->belongsToMany(Produit::class, 'variante_produit', 'id_taille', 'id_produit');
}
}

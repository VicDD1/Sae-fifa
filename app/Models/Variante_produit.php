<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Variante_produit extends Model
{
    use HasFactory;

    protected $table = 'variante_produit';
    protected $primaryKey = 'id_variante_produit';
    public $timestamps = false;

    protected $fillable = [
        'id_produit',
        'id_taille',
        'id_colori',
        'quantite_stock',
        'prix',
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit');
    }

    public function taille()
    {
        return $this->belongsTo(Taille::class, 'id_taille');
    }

    public function coloris()
    {
        return $this->belongsTo(Coloris::class, 'id_colori');
    }
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ligne_panier extends Model
{
    protected $table = 'ligne_panier';
    protected $primaryKey = 'id_ligne';

    public $timestamps = false;

    public $incrementing = true;
    protected $keyType = 'int';

    protected $fillable = [
        'id_panier',
        'id_produit',
        'id_colori',
        'id_taille',
        'quantitee'
    ];

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit');
    }

    public function taille()
    {
        return $this->belongsTo(\App\Models\Taille::class, 'id_taille', 'id_taille');
    }

    public function couleur()
    {
        return $this->belongsTo(\App\Models\Colori::class, 'id_colori', 'id_colori');
    }

}

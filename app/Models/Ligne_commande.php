<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ligne_commande extends Model
{
    protected $table = 'ligne_commande';  
    public $timestamps = false;

    protected $primaryKey = null;
    public $incrementing = false;

    protected $fillable = [
        'id_commande',
        'id_produit',
        'id_colori',
        'id_taille',
        'quantitee',
    ];

    /* Relations Eloquent */

    public function commande()
    {
        return $this->belongsTo(Commande::class, 'id_commande', 'id_commande');
    }

    public function produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit', 'id_produit');
    }

    public function taille()
    {
        return $this->belongsTo(Taille::class, 'id_taille', 'id_taille');
    }

    public function couleur()
    {
        return $this->belongsTo(Colori::class, 'id_colori', 'id_colori');
    }
}

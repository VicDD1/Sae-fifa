<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produit extends Model
{
    use HasFactory;

    protected $table = 'produit';
    protected $primaryKey = 'id_produit';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    public function variantes()
    {
        return $this->hasMany(Variante_produit::class, 'id_produit', 'id_produit');
    }

    public function couleurs()
    {
        return $this->belongsToMany(Colori::class, 'variante_produit', 'id_produit', 'id_colori')
                    ->distinct();
    }

    public function tailles()
    {
        return $this->belongsToMany(Taille::class, 'variante_produit', 'id_produit', 'id_taille')
                    ->distinct();
    }
}

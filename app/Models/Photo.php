<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;
    protected $table = 'photo';
    protected $primaryKey = 'id_photo';
    public $timestamps = false;
    public $incrementing = true;
    protected $keyType = 'int';
    protected $fillable = [
    'code_photo',
    'id_produit',
    'id_joueur'
    ];

    public function Produit()
    {
        return $this->belongsTo(Produit::class, 'id_produit');
    }
}

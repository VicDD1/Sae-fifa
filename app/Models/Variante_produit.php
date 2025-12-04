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
}

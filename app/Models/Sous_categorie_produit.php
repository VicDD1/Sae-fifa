<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Categorie_Produit extends Model
{
    use HasFactory;
    protected $table = "categorie_produit";
    protected $primaryKey = "sous_categorie";
    public $timestamps = false;
}
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Joueur_vote extends Model
{
    use HasFactory;
    protected $table = "joueur_vote  ";
    protected $primaryKey = "id_joueur_vote  ";
    public $timestamps = false;
}

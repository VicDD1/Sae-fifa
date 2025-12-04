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
}

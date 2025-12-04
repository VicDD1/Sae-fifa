<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Colori extends Model
{
    use HasFactory;
    protected $table = 'colori';
    protected $primaryKey = 'id_colori';
    public $timestamps = false; 
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Trophee extends Model
{
    use HasFactory;
    protected $table = 'trophee      ';
    protected $primaryKey = 'id_trophee      ';
    public $timestamps = false;

}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Publication extends Model
{
    use HasFactory;
    protected $table = 'publication ';
    protected $primaryKey = 'id_publication ';
    public $timestamps = false; 
    public $incrementing = true;
    protected $keyType = 'int';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competition extends Model
{
    use HasFactory;
    protected $table = "competition";
    protected $primaryKey = "id_competition";
    public $timestamps = false;
}

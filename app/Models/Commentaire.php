<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    protected $table = "Commentaire   "
    protected $primaryKey = "idCommentaire   "
    public $timestamps = false;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Theme_vote extends Model
{
    use HasFactory;
    protected $table = 'theme_vote';
    protected $primaryKey = 'id_theme_vote';
    public $timestamps = false;
}

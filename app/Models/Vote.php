<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vote extends Model
{
    protected $table = 'theme_vote';     // ta table exacte
    protected $primaryKey = 'id_theme';  // ta clé primaire exacte
    public $timestamps = false;          // si ta table n’a pas created_at / updated_at

    protected $fillable = [
        'nom_theme'
    ];
}

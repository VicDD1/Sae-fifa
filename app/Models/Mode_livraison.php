<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mode_livraison extends Model
{
    use HasFactory;

    protected $table = 'mode_livraison';
    protected $primaryKey = 'id_mode_livraison';
    public $timestamps = false;

    protected $fillable = [
        'type_livraison',
        'prix_mode_livraison',
        'id_service_expedition',
    ];
}

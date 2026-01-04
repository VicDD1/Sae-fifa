<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class reglement extends Model
{
    use HasFactory;
    protected $table = 'reglement';
    protected $primaryKey = 'id_reglement';
    public $timestamps = false;
        public function commande()
    {
        return $this->belongsTo(Commande::class);
    }
}

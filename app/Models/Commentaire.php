<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Commentaire extends Model
{
    use HasFactory;

    protected $table = 'commentaire';
    protected $primaryKey = 'id_commentaire';

    // On autorise ces champs
    protected $fillable = ['contenu', 'id_user_connecte', 'id_publication', 'idblog', 'parent_id'];

    // 1. L'auteur du commentaire
    public function user()
    {
        return $this->belongsTo(User_connecte::class, 'id_user_connecte', 'id_user_connecte');
    }

    // 2. Les réponses à ce commentaire (Enfants)
    public function replies()
    {
        return $this->hasMany(Commentaire::class, 'parent_id', 'id_commentaire');
    }
}
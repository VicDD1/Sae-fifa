<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    use HasFactory;

    // Nom exact de la table
    protected $table = 'blog';
    
    // Ta clé primaire est idblog
    protected $primaryKey = 'idblog';

    // Les colonnes modifiables
    protected $fillable = ['titre', 'description', 'resume', 'image_path', 'id_publication'];

    // Relation : Un blog a plusieurs commentaires
    public function commentaires()
    {
        // On ne charge que les messages principaux (ceux qui n'ont pas de parent)
        // On précise bien les clés locales et étrangères
        return $this->hasMany(Commentaire::class, 'idblog', 'idblog')->whereNull('parent_id');
    }
}
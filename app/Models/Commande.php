<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Commande extends Model
{
    protected $table = 'commande';               // nom exact de la table
    protected $primaryKey = 'id_commande';       // clé primaire

    public $timestamps = false;                  // pas de created_at / updated_at

    protected $fillable = [
        'id_adresse',
        'id_user_connecte',
        'id_acheteur',
        'id_mode_livraison',
        'point_relais_nom',
        'point_relais_adresse',
        'date_commande',
        'montant_total',
        'date_paiement',
        'mode_paiement',
        'statut_paiement',
        'statut_livraison',
        'commentaire_sav',
        'date_livraison_reelle',
    ];

    /* Relations possibles : */

    public function utilisateur()
    {
        return $this->belongsTo(User::class, 'id_user_connecte', 'id_user_connecte');
    }

    public function acheteur()
    {
        return $this->belongsTo(Acheteur::class, 'id_acheteur', 'id_acheteur');
    }

    public function adresse()
    {
        return $this->belongsTo(Adresse::class, 'id_adresse', 'id_adresse');
    }

    public function modeLivraison()
    {
        return $this->belongsTo(Mode_livraison::class, 'id_mode_livraison', 'id_mode_livraison');
    }

    public function lignes()
    {
        return $this->hasMany(Ligne_commande::class, 'id_commande', 'id_commande');
    }
        public function reglements()
    {
        return $this->hasMany(Reglement::class);
    }
}

<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Carbon\Carbon;

class UpdateLastLoginTimestamp
{
    public function handle(Login $event)
    {
        // $event->user contient l'utilisateur qui vient de se connecter
        $user = $event->user;

        // On force la mise à jour de la colonne updated_at
        $user->updated_at = now();
        
        // On sauvegarde sans déclencher d'autres événements si on veut être discret
        $user->save();
    }
}
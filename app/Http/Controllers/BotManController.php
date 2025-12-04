<?php
namespace App\Http\Controllers;
use BotMan\BotMan\BotMan;
use Illuminate\Http\Request;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;
class BotManController extends Controller
{
    public function handle()
    {
        $botman = app('botman');
        $botman->hears('.*problème.*chargement.*|chargement.*page.*',
    function (BotMan $bot) {
        $response = "Je suis désolé d'entendre que vous rencontrez des problèmes de chargement de pages. Voici quelques suggestions :\n1. Assurez-vous d'avoir une connexion Internet stable.\n2. Essayez de rafraîchir la page.\n3. Vérifiez si d'autres navigateurs ou appareils fonctionnent correctement.";
        $bot->reply($response);
    });
    $botman->hears('.*problème.*navigation.*|navigation.*|section.*promotions.*',
    function (BotMan $bot) {
        $response = "Si vous avez des problèmes de navigation ou si vous ne trouvez pas la section des promotions, essayez ceci :\n1. Vérifiez le menu de navigation principal.\n2. Utilisez la barre de recherche pour trouver des promotions spécifiques.\n3. Essayez de vider le cache de votre navigateur.";
                $bot->reply($response);
            });
            $botman->listen();
        }
    }
?>

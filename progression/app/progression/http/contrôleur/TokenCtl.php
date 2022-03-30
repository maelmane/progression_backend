<?php

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use progression\domaine\entité\{User};

class TokenCtl extends Contrôleur
{
    public function post(Request $request, $username) {
        $ressources = $request->input("ressources");
        $expiration = time() + $_ENV["JWT_TTL"]; //TODO: redéfinir pour une plus longue durée.
        $user = new User($username);

        $token = GénérateurDeToken::get_instance()->générer_token($user, $ressources, $expiration);
        //$réponse = $token;
        $réponse = $this->préparer_réponse($token);
        Log::debug("TokenCtl.post. Réponse : ", [$réponse]);
        
        return $réponse;
    }
}

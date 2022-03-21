<?php

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JetonCtl extends Contrôleur
{
    public function post(Request $request, $username) {
        $idRessource = $request->input("idRessource");
        $typeRessource = $request->input("typeRessource");
        $token = GénérateurDeToken::get_instance()->générer_token_pour_ressource($username, $typeRessource, $idRessource);
        $réponse = $this->préparer_réponse($token);
        Log::debug("JetonCtl.post. Retour : ", [$réponse]);
        return $réponse;
    }
}

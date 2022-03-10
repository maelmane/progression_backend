<?php

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class JetonCtl extends Contrôleur
{
    /**
    * Génère un JWT qui donne accès à une ressource (ex.: un avancement).
    *
    * @param Request $request Les informations identifiant la ressource et un $username de l'étudiant concerné.
    *
    * @return string Un URL qui donne accès à la ressource. 
    */
    public function post(Request $request, $username) {
        $idRessource = $request->input("idRessource");
        $typeRessource = $request->input("typeRessource");
        
        $token = GénérateurDeToken::get_instance()->générer_token_pour_ressource($username, $idRessource, $typeRessource);
        $réponse = $this->préparer_réponse($token);
        Log::debug("JetonCtl.post. Retour : ", [$réponse]);
        
        return $réponse;
    }
}
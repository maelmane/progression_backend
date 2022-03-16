<?php
/*
   This file is part of Progression.

   Progression is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Progression is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Progression.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace progression\http\contrôleur;

use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Log;

class GénérateurDeToken
{
    private static ?GénérateurDeToken $instance = null;

    private function __construct()
    {
    }

    static function get_instance()
    {
        if (GénérateurDeToken::$instance == null) {
            GénérateurDeToken::$instance = new GénérateurDeToken();
        }

        return GénérateurDeToken::$instance;
    }

    static function set_instance(GénérateurDeToken $générateur)
    {
        GénérateurDeToken::$instance = $générateur;
    }

    function générer_token($user)
    {
        Log::debug("GénérateurDeToken.générer_token. Params : ", [$user]);

        $payload = [
            "username" => $user->username,
            "current" => time(),
            "expired" => time() + $_ENV["JWT_TTL"],
        ];

        $réponse = JWT::encode($payload, $_ENV["JWT_SECRET"], "HS256");

        Log::debug("InscriptionCtl.générer_token. Retour : ", [$réponse]);
        return $réponse;
    }

    /**
    * Génère un token JWT en fonction de paramètres décrivant une ressource. 
    *
    * @param string $user L'utilisateur associé à la ressource.
    * @param string $typeResource Le type de la ressource (ex.: avancement).
    * @param string $ideRessource L'identifiant de la ressource ($username.$uriQuestion).
    * @param string $method La méthode de requête http.
    *
    * @return string Un JWT signé par le serveur. 
    */
    function générer_token_pour_ressource($user, $typeRessource, $idRessource, $method=["get"])
    {
        $payload = [
            "username" => $user,
            "ressource" => $typeRessource,
            "id" => $idRessource,
            "method" => $method,
        ];

        $JTW = JWT::encode($payload, $_ENV["JWT_SECRET"], "HS256");

        Log::debug("GénérateurDeToken.générer_token_pour_ressource. Retour : ", [$JTW]);
        return $JTW;
    }
}

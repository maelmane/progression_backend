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

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use progression\domaine\interacteur\ObtenirAvancementInt;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\domaine\interacteur\SauvegarderAvancementInt;
use progression\http\transformer\AvancementTransformer;
use progression\util\Encodage;
use progression\domaine\entité\{User, Avancement, Question};
use Firebase\JWT\JWT;
use Exception;

class RessourceCtl extends Contrôleur {

    public function get(Request $request) {
        $tokenRessource = $request->input("tokenRessource");

        $tokenRessourceDécodé = null;

        try {
			$tokenRessourceDécodé = JWT::decode($tokenRessource, $_ENV["JWT_SECRET"], ["HS256"]);
		} catch (Exception $e) {
			//TODO: Logger erreur.
		}

        //TODO: valider si c'est une ressource de type question qui est demandé, sinon retourner erreur. 
        //TODO: effectuer le login avec clé et username!!!

        $username = $tokenRessourceDécodé->username;
        $question_uri = $tokenRessourceDécodé->uri;

        Log::debug("RessourceCtl.get. Params : ", [$request->all(), $username, $question_uri]);

		$avancement = $this->obtenir_avancement($username, $question_uri);
		$réponse = $this->valider_et_préparer_réponse($avancement, $username, $question_uri);

		Log::debug("RessourceCtl.get. Retour : ", [$réponse]);
		return $réponse;
    }


	private function avancement_to_array($avancement)
	{
		Log::debug("AvancementCtl.avancement_to_array. Params : ", [$avancement]);

		$réponse = $this->item($avancement, new AvancementTransformer());

		Log::debug("AvancementCtl.avancement_to_array. Retour : ", [$réponse]);

		return $réponse;
	}

    private function obtenir_avancement($username, $question_uri)
	{
		Log::debug("AvancementCtl.obtenir_avancement. Params : ", [$username, $question_uri]);

		$avancementInt = new ObtenirAvancementInt();
		$chemin = Encodage::base64_decode_url($question_uri);
		$avancement = $avancementInt->get_avancement($username, $chemin);

		Log::debug("AvancementCtl.obtenir_avancement. Retour : ", [$avancement]);
		return $avancement;
	}

    private function valider_et_préparer_réponse($avancement, $username, $question_uri)
	{
		Log::debug("AvancementCtl.valider_et_préparer_réponse. Params : ", [$avancement, $username, $question_uri]);

		if ($avancement) {
			$avancement->id = "{$username}/$question_uri";
			$réponse_array = $this->avancement_to_array($avancement);
		} else {
			$réponse_array = null;
		}

		$réponse = $this->préparer_réponse($réponse_array);

		Log::debug("AvancementCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);
		return $réponse;
	}
}
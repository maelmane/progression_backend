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
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use progression\domaine\entité\Commentaire;
use progression\domaine\interacteur\SauvegarderCommentaireInt;
use progression\http\transformer\CommentaireTransformer;
use progression\dao\exécuteur\ExécutionException;
use progression\util\Encodage;

class CommentaireCtl extends Contrôleur
{
	public function post(Request $request, $username, $question_uri, $timestamp)
	{
		Log::debug("CommentaireCtl.post. Params : ", [$request->all(), $username]);
		$commentaire = null;
		$réponse = null;

		$question_uriDécodé = Encodage::base64_decode_url($question_uri);

		$validateur = $this->valider_paramètres($request);
		if ($validateur->fails()) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ") Paramètres invalides",
			);
			return $this->réponse_json(["erreur" => $validateur->errors()], 400);
		}

		$commentaireInt = new SauvegarderCommentaireInt();
		$commentaire = $commentaireInt->sauvegarder_commentaire(
			$username,
			$question_uriDécodé,
			$timestamp,
			null,
			new Commentaire(
				$request->message,
				$request->créateur,
				(new \DateTime())->getTimestamp(),
				$request->numéro_ligne,
			),
		);

		$numéro = array_key_first($commentaire);

		$commentaire[$numéro]->id = "{$username}/{$question_uri}/{$timestamp}/{$numéro}";
		$réponse = $this->item($commentaire[$numéro], new CommentaireTransformer());
		return $this->préparer_réponse($réponse);
	}

	private function valider_paramètres($request)
	{
		return Validator::make(
			$request->all(),
			[
				"message" => "required",
				"créateur" => "required",
				"numéro_ligne" => ["required", "integer"],
			],
			[
				"required" => "Le champ :attribute est obligatoire.",
				"integer" => "Le champ :attribute doit être un entier.",
			],
		);
	}
}

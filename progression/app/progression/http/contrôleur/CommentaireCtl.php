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

use Illuminate\Http\{Request, JsonResponse};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use progression\domaine\entité\Commentaire;
use progression\domaine\interacteur\{SauvegarderCommentaireInt, IntéracteurException};
use progression\http\transformer\CommentaireTransformer;
use progression\util\Encodage;

class CommentaireCtl extends Contrôleur
{
	public function post(Request $request, $username, $question_uri, $timestamp): JsonResponse
	{
		Log::debug("CommentaireCtl.post. Params : ", [$request->all(), $username]);
		$commentaire = null;

		$question_uriDécodé = Encodage::base64_decode_url($question_uri);

		$réponse = null;
		$validateur = $this->valider_paramètres($request);
		if ($validateur->fails()) {
			$réponse = $this->réponse_json(["erreur" => $validateur->errors()], 400);
		} else {
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

			if (count($commentaire) > 0) {
				$numéro = array_key_first($commentaire);

				$commentaire[$numéro]->id = $numéro;

				$réponse = $this->valider_et_préparer_réponse(
					$numéro !== null ? $commentaire[$numéro] : null,
					$username,
					$question_uri,
					$timestamp,
				);
			}
		}

		Log::debug("CommentaireCtl.post. Retour : ", [$réponse]);
		return $réponse;
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
				"required" => "Err: 1004. Le champ :attribute est obligatoire.",
				"integer" => "Err: 1003. Le champ :attribute doit être un entier.",
			],
		);
	}

	private function valider_et_préparer_réponse(
		Commentaire $commentaire,
		string $username,
		string $question_uri,
		int $timestamp,
	): JsonResponse {
		Log::debug("CommentaireCtl.valider_et_préparer_réponse. Params : ", [
			$commentaire,
			$username,
			$question_uri,
			$timestamp,
		]);

		$réponse = $this->item($commentaire, new CommentaireTransformer("{$username}/{$question_uri}/{$timestamp}"));

		$réponse = $this->préparer_réponse($réponse);

		Log::debug("CommentaireCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);

		return $réponse;
	}
}

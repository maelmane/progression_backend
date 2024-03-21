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

use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Enum;
use Illuminate\Validation\Validator as ValidatorImpl;
use progression\http\transformer\UserTransformer;
use progression\http\transformer\dto\UserDTO;
use progression\domaine\entité\user\{User, État, Rôle};
use progression\domaine\interacteur\PermissionException;
use progression\domaine\interacteur\{ObtenirUserInt, IntéracteurException, ModifierUserInt, SauvegarderUtilisateurInt};
use DomainException;

class UserModificationCtl extends UserCtl
{
	public function patch(Request $request, string $username): JsonResponse
	{
		Log::debug("UserModificationCtl.patch. Params : ", [$request->all(), $username]);

		$réponse = null;
		$validation = $this->valider_paramètres($request);
		$erreurs = $validation->errors();
		if (count($erreurs) > 0) {
			return $this->réponse_json(["erreur" => $validation->errors()], 400);
		}

		try {
			$user = $this->obtenir_user($username);
			if ($user) {
				$user = $this->modifier_user($username, $user, $request);
				$réponse = $this->valider_et_préparer_réponse($user, $user->username);
			} else {
				$réponse = $this->valider_et_préparer_réponse(null, null);
			}
		} catch (DomainException $e) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ") " . $e->getMessage(),
			);
			return $this->réponse_json(["erreur" => $e->getMessage()], 400);
		} catch (PermissionException $e) {
			Log::notice(
				"({$request->ip()}) - {$request->method()} {$request->path()} (" . __CLASS__ . ") " . $e->getMessage(),
			);
			return $this->réponse_json(["erreur" => $e->getMessage()], 403);
		}

		Log::debug("UserModificationCtl.patch. Retour : ", [$réponse]);
		return $réponse;
	}

	private function modifier_user(string $username, User $user, Request $request): User
	{
		if (array_intersect(array_keys($request->all()), ["courriel", "état", "préférences", "rôle"])) {
			$user_original = clone $user;
			$user_modifié = $this->modifier_entité($username, $user, $request);

			if ($user_modifié != $user_original) {
				$userInt = new SauvegarderUtilisateurInt();
				$user = $userInt->sauvegarder_user($username, $user)[$username];
			}
		}

		if (array_key_exists("password", $request->all())) {
			$this->modifier_mot_de_passe($user, $request["password"]);
		}
		if (array_key_exists("nom", $request->all())) {
			$this->modifier_nom_utilisateur($user, $request["nom"]);
		}
		if (array_key_exists("prénom", $request->all())) {
			$this->modifier_prenom_utilisateur($user, $request["prénom"]);
		}
		if (array_key_exists("nom_complet", $request->all())) {
			$this->modifier_nom_complet_utilisateur($user, $request["nom_complet"]);
		}
		if (array_key_exists("biographie", $request->all())) {
			$this->modifier_biographie_utilisateur($user, $request["biographie"]);
		}

		return $user;
	}

	private function modifier_entité(string $username, User $user, Request $request): User
	{
		if (array_key_exists("état", $request->all())) {
			$user = (new ModifierUserInt())->modifier_état($user, État::from($request["état"]));
		}
		if (array_key_exists("rôle", $request->all())) {
			$user = (new ModifierUserInt())->modifier_rôle($user, Rôle::from($request["rôle"]));
		}
		if (array_key_exists("préférences", $request->all())) {
			$user = (new ModifierUserInt())->modifier_préférences($user, $request["préférences"]);
		}
		if (array_key_exists("courriel", $request->all())) {
			$user = (new ModifierUserInt())->modifier_courriel($user, $request["courriel"]);
		}
		if (array_key_exists("nom", $request->all())) {
			$user = (new ModifierUserInt())->modifier_nom($user, $request["nom"]);
		}
		if (array_key_exists("prénom", $request->all())) {
			$user = (new ModifierUserInt())->modifier_prenom($user, $request["prénom"]);
		}
		if (array_key_exists("nom_complet", $request->all())) {
			$user = (new ModifierUserInt())->modifier_nomComplet($user, $request["nom_complet"]);
		}
		if (array_key_exists("biographie", $request->all())) {
			$user = (new ModifierUserInt())->modifier_biographie($user, $request["biographie"]);
		}
		if (array_key_exists("pseudo", $request->all())) {
			$user = (new ModifierUserInt())->modifier_pseudo($user, $request["pseudo"]);
		}
		if (array_key_exists("avatar", $request->all())) {
			$user = (new ModifierUserInt())->modifier_avatar($user, $request["avatar"]);
		}
		if (array_key_exists("occupation", $request->all())) {
			$user = (new ModifierUserInt())->modifier_occupation($user, $request["occupation"]);
		}
		return $user;
	}

	private function modifier_mot_de_passe(User $user, string $password): void
	{
		(new ModifierUserInt())->modifier_password($user, $password);
	}

	private function modifier_nom_utilisateur(User $user, string $nom): void
	{
		(new ModifierUserInt())->modifier_nom($user, $nom);
	}

	private function modifier_prenom_utilisateur(User $user, string $prenom): void
	{
		(new ModifierUserInt())->modifier_prenom($user, $prenom);
	}

	private function modifier_nom_complet_utilisateur(User $user, string $nomComplet): void
	{
		(new ModifierUserInt())->modifier_nomComplet($user, $nomComplet);
	}

	private function modifier_biographie_utilisateur(User $user, string $biographie): void
	{
		(new ModifierUserInt())->modifier_biographie($user, $biographie);
	}

	private function valider_paramètres(Request $request): ValidatorImpl
	{
		$validateur = Validator::make(
			$request->all(),
			[
				"préférences" => "sometimes|string|json|between:0,65535",
				"état" => ["sometimes", "string", new Enum(État::class)],
				"rôle" => ["sometimes", "string", new Enum(Rôle::class)],
				"courriel" => "sometimes|email",
				"password" => "sometimes|string|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/u",
				"nom" => "sometimes|string|max:255",
				"prenom" => "sometimes|string|max:255",
				"nom_complet" => "sometimes|string|max:255",
				"biographie" => "sometimes|string|max:255",
			],
			[
				"json" => "Le champ :attribute doit être en format json.",
				"préférences.between" =>
					"Le champ :attribute " . mb_strlen($request->paramètres) . " > :max caractères.",
				"password.regex" => "Le mot de passe doit contenir au moins 8 caractères, une majuscule et un chiffre.",
			],
		);

		return $validateur;
	}
}

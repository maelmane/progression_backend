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

namespace progression\domaine\interacteur;

use progression\domaine\entité\user\{User, État, Rôle};
use progression\http\transformer\UserTransformer;
use progression\http\contrôleur\GénérateurDeToken;
use Carbon\Carbon;

class InscriptionInt extends Interacteur
{
	public function effectuer_inscription_locale(
		string $username,
		string $courriel,
		string|null $password,
		Rôle $rôle = Rôle::NORMAL,
	): User|null {
		$dao = $this->source_dao->get_user_dao();
		$user = $dao->get_user($username);
		if (!$user && $password) {
			$user_créé = $this->effectuer_inscription_avec_mdp($username, $courriel, $password, $rôle);

			if ($user_créé && $user_créé->rôle != Rôle::ADMIN) {
				$this->envoyer_courriel_de_validation($user_créé);
			}

			return $user_créé;
		} elseif ($user && !$password) {
			return $this->effectuer_renvoi_de_courriel($user) ? $user : null;
		}

		return null;
	}

	private function effectuer_renvoi_de_courriel(User $user): bool
	{
		if ($user->état == État::ATTENTE_DE_VALIDATION) {
			$this->envoyer_courriel_de_validation($user);
			return true;
		}
		return false;
	}

	public function effectuer_inscription_sans_mdp(
		string $username,
		string $courriel = null,
		Rôle $rôle = Rôle::NORMAL,
	): User|null {
		$dao = $this->source_dao->get_user_dao();
		return $dao->get_user($username) ??
			$dao->save(
				new User(
					username: $username,
					date_inscription: Carbon::now()->getTimestamp(),
					courriel: $courriel,
					rôle: $rôle,
					état: État::ACTIF,
					préférences: getenv("PREFERENCES_DEFAUT") ?: "",
				),
			);
	}

	private function effectuer_inscription_avec_mdp(
		string $username,
		string $courriel,
		string $password,
		Rôle $rôle,
	): User|null {
		$dao = $this->source_dao->get_user_dao();
		if ($dao->trouver(courriel: $courriel)) {
			return null;
		}

		$user = $this->créer_etsauvegarder_user($username, $courriel, $password, $rôle);

		return $user;
	}

	private function créer_etsauvegarder_user(
		string $username,
		string $courriel,
		string $password,
		Rôle $rôle,
	): User|null {
		$dao = $this->source_dao->get_user_dao();
		$user = $dao->save(
			new User(
				username: $username,
				date_inscription: Carbon::now()->getTimestamp(),
				courriel: $courriel,
				rôle: $rôle,
				état: $rôle == Rôle::ADMIN ? État::ACTIF : État::ATTENTE_DE_VALIDATION,
				préférences: getenv("PREFERENCES_DEFAUT") ?: "",
			),
		);
		$dao->set_password($user, $password);

		return $user;
	}

	private function envoyer_courriel_de_validation(User $user): void
	{
		$data = [
			"url_user" => getenv("APP_URL") . "/user/" . $user->username,
			"user" => [
				"username" => $user->username,
				"courriel" => $user->courriel,
				"rôle" => $user->rôle,
			],
		];
		$ressources = [
			"user" => [
				"url" => "^user/" . $user->username . "$",
				"method" => "^POST$",
			],
		];

		$expirationToken = Carbon::now()->addMinutes((int) getenv("JWT_EXPIRATION"))->timestamp;
		$token = GénérateurDeToken::get_instance()->générer_token(
			$user->username,
			$expirationToken,
			$ressources,
			$data,
		);
		$this->source_dao->get_expéditeur()->envoyer_validation_courriel($user, $token);
	}
}

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
use progression\dao\mail\EnvoiDeCourrielException;
use progression\dao\UserDAO;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Gate;

class ModifierUserInt extends Interacteur
{
	public function modifier_état(User $user, État $état): User
	{
		if (($user->état == État::INACTIF || $état == État::INACTIF) && !Gate::allows("modifier-état-user-inactif")) {
			throw new PermissionException("Transition d'état interdite");
		}
		$user->état = $état;

		return $user;
	}

	public function modifier_rôle(User $user, Rôle $rôle): User
	{
		if ($rôle == Rôle::ADMIN && !Gate::allows("modifier-rôle-user-admin")) {
			throw new PermissionException("Transition de rôle interdite");
		}

		$user->rôle = $rôle;

		return $user;
	}

	public function modifier_courriel(User $user, string $courriel): User
	{
		if ($user->courriel == $courriel) {
			return $user;
		}

		$dao = $this->source_dao->get_user_dao();
		$user_courriel = $dao->trouver(courriel: $courriel);
		if ($user_courriel?->username && $user_courriel->username !== $user->username) {
			throw new DuplicatException("Le courriel est déjà utilisé.");
		}

		$user->courriel = $courriel;
		if (Gate::allows("valider-le-courriel", $user->rôle)) {
			$user->état = État::EN_ATTENTE_DE_VALIDATION;
			$this->envoyer_courriel_de_validation($user);
		}

		return $user;
	}

	public function modifier_préférences(User $user, string $préférences): User
	{
		$user->préférences = $préférences;

		return $user;
	}

	public function modifier_password(User $user, string $password): User
	{
		$dao = $this->source_dao->get_user_dao();
		$dao->set_password($user, $password);

		return $user;
	}

	public function modifier_nom(User $user, string $nom):User{
		$dao = $this->source_dao->get_user_dao();
    	$dao->set_nom($user, $nom);
		return $user;
	}

	//méthode de modification
	public function modifier_prenom(User $user, string $prenom):User{
		$dao = $this->source_dao->get_user_dao();
    	$dao->set_prenom($user, $prenom);
		return $user;
	}

	public function modifier_nomComplet(User $user, string $nomComplet):User{
		$dao = $this->source_dao->get_user_dao();
    	$dao->set_nomComplet($user, $nomComplet);
		return $user;
	}

	public function modifier_biographie(User $user, string $biographie):User{
		$dao = $this->source_dao->get_user_dao();
    	$dao->set_biographie($user, $biographie);
		return $user;
	}



	private function envoyer_courriel_de_validation(User $user): void
	{
		try {
			$this->source_dao->get_expéditeur()->envoyer_courriel_de_validation($user);
		} catch (EnvoiDeCourrielException $e) {
			Log::notice("Échec de l'envoi du courriel à " . $user->courriel);
			Log::notice($e->getMessage());
			Log::debug($e);
		}
	}
}

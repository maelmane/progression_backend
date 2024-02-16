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
use progression\http\transformer\UserTransformer;
use progression\http\transformer\dto\UserDTO;
use progression\domaine\entité\user\{User, État};
use progression\domaine\entité\Avancement;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\util\Encodage;
use DomainException;

class ProfilCtl extends Contrôleur
{
	public function get(Request $request, string $username): JsonResponse
	{
		Log::debug("ProfilCtl.get. Params : ", [$request->all(), $username]);

		$réponse = null;
		return $réponse;
	}

  // get_liens() ?

  protected function obtenir_user(string $username): User|null
	{
		Log::debug("profilCtl.obtenir_profil. Params : ", [$user]); // à valider

		$profilInt = new ObtenirProfilInt();

		$profil = $profilInt->get_profil(username: $username);

		Log::debug("ProfilCtl.obtenir_profil. Retour : ", [$profil]);
		return $profil;
	}

  private function valider_et_préparer_réponse($user, $id)
	{

    //valider à faire

    $réponse = $this->préparer_réponse($réponse);

    Log::debug("ProfilCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);

		return $réponse;
	}
}
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
use progression\http\transformer\UserTransformer;
use progression\domaine\interacteur\ObtenirUserInt;

class UserCtl extends Contrôleur
{
	public function get(Request $request, $username = null)
	{
		Log::debug("UserCtl.get. Params : ", [$request->all(), $username]);

		$user = $this->obtenir_user($username ?? $request->user()->username);

		$réponse = $this->valider_et_préparer_réponse($user);
		Log::debug("UserCtl.get. Retour : ", [$réponse]);
		return $réponse;
	}

	private function obtenir_user($username)
	{
		Log::debug("UserCtl.obtenir_user. Params : ", [$username]);

		$userInt = new ObtenirUserInt();
		$user = null;

		if ($username != null && $username != "") {
			$user = $userInt->get_user($username);
		}

		Log::debug("UserCtl.obtenir_user. Retour : ", [$user]);
		return $user;
	}

	private function valider_et_préparer_réponse($user)
	{
		Log::debug("UserCtl.valider_et_préparer_réponse. Params : ", [$user]);

		$réponse_array = $this->item($user, new UserTransformer());

		$réponse = $this->préparer_réponse($réponse_array);

		Log::debug("UserCtl.valider_et_préparer_réponse. Retour : ", [$réponse]);

		return $réponse;
	}
}

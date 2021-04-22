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
use progression\http\transformer\UserTransformer;
use progression\domaine\interacteur\ObtenirUserInt;

class UserCtl extends Contrôleur
{
	public function get(Request $request, $username = null)
	{
		$userInt = new ObtenirUserInt();
		$user = null;

		if ($username == null) {
			$username = $request->request->get("utilisateurConnecté")->username;
		}

		if ($username != null && $username != "") {
			$user = $userInt->get_user($username);
		}

		$réponse = $this->item($user, new UserTransformer());

		return $this->préparer_réponse($réponse);
	}
}

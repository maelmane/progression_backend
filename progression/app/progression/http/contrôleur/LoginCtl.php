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

use Exception;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use progression\domaine\interacteur\LoginInt;
use progression\dao\DAOFactory;

class LoginCtl extends Contrôleur
{
	public function login(Request $request)
	{
		$loginInt = $this->intFactory->getLoginInt();
		$username = $request->input("username");
		$password = $request->input("password");
		$user = null;
		$token = null;

		$user = $loginInt->effectuer_login($username, $password);

		if ($user != null) {
			$payload = [
				"user" => $user,
				"current" => time(),
				"expired" => time() + $_ENV["JWT_TTL"],
			];
			$token = JWT::encode($payload, $_ENV["JWT_SECRET"], "HS256");
		}

		return $this->préparer_réponse(["Token" => $token]);
	}
}

?>

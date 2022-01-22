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

class ConfigCtl extends Contrôleur
{
	public function get(Request $request)
	{
		Log::debug("ConfigCtl.get");

		$config = [
			"AUTH" => [
				"LDAP" => $_ENV["AUTH_LDAP"],
				"LOCAL" => $_ENV["AUTH_LOCAL"],
			],
		];

		$config_ldap = [
			"DOMAINE" => $_ENV["LDAP_DOMAINE"],
		];

		if ($_ENV["AUTH_LDAP"] == "true") {
			$config["LDAP"] = $config_ldap;
		}

		$réponse = $this->préparer_réponse($config);

		Log::debug("ConfigCtl.get. Retour : ", [$réponse]);
		return $réponse;
	}
}

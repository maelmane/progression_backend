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
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use progression\http\transformer\TokenTransformer;

class TokenCtl extends Contrôleur
{
	public function post(Request $request, string $username): JsonResponse
	{
		Log::debug("TokenCtl.post. Params : ", [$request->all()]);

		$ressources = $request->input("ressources");

		$expirationToken = $request->input("expiration") ?? 0;
		$token = GénérateurDeToken::get_instance()->générer_token($username, $expirationToken, $ressources);
		$réponse = $this->préparer_réponse($this->item($token, new TokenTransformer($username)));

		Log::debug("TokenCtl.post. Réponse : ", [$réponse]);

		return $réponse;
	}
}

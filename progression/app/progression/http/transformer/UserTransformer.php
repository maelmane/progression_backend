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

namespace progression\http\transformer;

use progression\domaine\entité\User;
use League\Fractal;

class UserTransformer extends Fractal\TransformerAbstract
{
	public $type = "User";

	public function transform(User $user)
	{
		$data = [
			"id" => $user->username,
			"username" => $user->username,
			"rôle" => $user->role,
			"links" => [
				"self" => $_ENV["APP_URL"] . "user/" . $user->username,
			],
		];

		return $data;
	}
}

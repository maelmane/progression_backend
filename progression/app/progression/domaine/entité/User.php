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

namespace progression\domaine\entité;

use InvalidArgumentException;

class User
{
	const RÔLE = Rôle::class;
	const ROLE = Rôle::class;
	const ÉTAT = État::class;

	public $username;
	public string|null $courriel;
	public État $état = État::INACTIF;
	public Rôle $rôle = Rôle::NORMAL;
	public $avancements;
	public $clés;
	public string $préférences;

	public function __construct(
		$username,
		string|null $courriel = null,
		État $état = État::INACTIF,
		Rôle $rôle = Rôle::NORMAL,
		$avancements = [],
		$clés = [],
		string $préférences = "",
	) {
		$this->username = $username;
		$this->courriel = $courriel;
		$this->état = $état;
		$this->rôle = $rôle;
		$this->avancements = $avancements;
		$this->clés = $clés;
		$this->préférences = $préférences;
	}
}

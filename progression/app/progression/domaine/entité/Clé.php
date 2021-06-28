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

class Clé
{
	const PORTEE_REVOQUEE = 0;
	const PORTEE_AUTH = 1;

	public $numéro;
	public $création;
	public $expiration;
	public $portée;

	public function __construct($numéro, $création, $expiration, $portée)
	{
		$this->numéro = $numéro;
		$this->création = $création;
		$this->expiration = $expiration;
		$this->portée = $portée;
	}
}

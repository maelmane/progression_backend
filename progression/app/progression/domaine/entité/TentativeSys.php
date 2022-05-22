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

class TentativeSys extends Tentative
{
	public $conteneur;
	public $réponse;
	public $tests_réussis;
	public $résultats;

	public function __construct(
		$conteneur = null,
		$réponse = null,
		$date_soumission = null,
		$réussi = false,
		$tests_réussis = 0,
		$temps_exécution = null,
		$feedback = null,
		$résultats = [],
		$commentaires = []
	) {
		parent::__construct($date_soumission, $réussi, $temps_exécution, $feedback, $commentaires);
		$this->conteneur = $conteneur;
		$this->réponse = $réponse;
		$this->tests_réussis = $tests_réussis;
		$this->résultats = $résultats;
	}
}

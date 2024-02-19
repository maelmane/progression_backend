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
	public string|null $conteneur_id;
	public string|null $url_terminal;
	public $réponse;

	public function __construct(
		string $conteneur_id = null,
		string $url_terminal = null,
		$réponse = null,
		$date_soumission = 0,
		$réussi = false,
		$résultats = [],
		$tests_réussis = 0,
		$temps_exécution = null,
		$feedback = null,
		$commentaires = [],
	) {
		parent::__construct(
			$date_soumission,
			$réussi,
			$résultats,
			$tests_réussis,
			$temps_exécution,
			$feedback,
			$commentaires,
		);
		$this->conteneur_id = $conteneur_id;
		$this->url_terminal = $url_terminal;
		$this->réponse = $réponse;
	}
}

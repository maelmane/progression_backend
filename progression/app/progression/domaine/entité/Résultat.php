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

class Résultat
{
	public $sortie_observée;
	public $sortie_erreur;
	public int $code_retour;
	public $résultat;
	public $feedback;
	public $temps_exécution;

	public function __construct(
		$sortie_observée = "",
		$sortie_erreur = "",
		$résultat = false,
		$feedback = null,
		$temps_exécution = null,
		int $code_retour = 0,
	) {
		$this->résultat = $résultat;
		$this->feedback = $feedback;
		$this->sortie_observée = $sortie_observée;
		$this->sortie_erreur = $sortie_erreur;
		$this->code_retour = $code_retour;
		$this->temps_exécution = $temps_exécution;
	}
}

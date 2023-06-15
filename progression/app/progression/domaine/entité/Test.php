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

class Test
{
	public $nom;
	public $sortie_attendue;
	public bool $sortie_cachée;
	public $feedback_pos;
	public $feedback_neg;
	public $feedback_err;

	public function __construct(
		$nom,
		$sortie_attendue,
		$feedback_pos = null,
		$feedback_neg = null,
		$feedback_err = null,
		bool $sortie_cachée = false
	) {
		$this->nom = $nom;
		$this->sortie_attendue = $sortie_attendue;
		$this->sortie_cachée = $sortie_cachée;
		$this->feedback_pos = $feedback_pos;
		$this->feedback_neg = $feedback_neg;
		$this->feedback_err = $feedback_err;
	}
}

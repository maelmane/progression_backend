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

class Avancement
{
	public $tentatives;
	public $sauvegardes;
	public $etat;
	public $type;
	public $titre;
	public $niveau;
	public $date_modification;
	public $date_réussite;

	public function __construct(
		$etat = Question::ETAT_DEBUT,
		$type = Question::TYPE_INCONNU,
		$tentatives = [],
		$sauvegardes = [],
		$titre = "",
		$niveau = "",
		$date_modification,
		$date_réussite
	) {
		$this->tentatives = $tentatives;
		$this->sauvegardes = $sauvegardes;
		$this->etat = $etat;
		$this->type = $type;
		$this->titre = $titre;
		$this->niveau = $niveau;
		$this->date_modification = $date_modification;
		$this->date_réussite = $date_réussite;
	}
}

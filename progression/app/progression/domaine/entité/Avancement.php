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
    public $type="prog";
	public $etat;
	public $tentatives;
	public $titre;
	public $niveau;
	public $date_modification;
	public $date_réussite;
	public $sauvegardes;
	public string|null $extra;

	/**
	 * @param array<Tentative> $tentatives
	 * @param array<Sauvegarde> $sauvegardes
	 **/
	public function __construct(
		array $tentatives = [],
		$titre = "",
		$niveau = "",
		array $sauvegardes = [],
		string|null $extra = ""
	) {
		$this->etat = Question::ETAT_DEBUT;
		$this->tentatives = $tentatives;
		$this->titre = $titre;
		$this->niveau = $niveau;
		$this->date_modification = null;
		$this->date_réussite = null;
		$this->sauvegardes = $sauvegardes;
		$this->extra = $extra;

		$this->mettre_à_jour_dates_et_état();
	}

	public function ajouter_tentative($tentative, $i = null)
	{
		if ($tentative->date_soumission > $this->date_modification) {
			$this->date_modification = $tentative->date_soumission;
		}
		if ($tentative->réussi) {
			$this->etat = Question::ETAT_REUSSI;
			if (!$this->date_réussite || $tentative->date_soumission < $this->date_réussite) {
				$this->date_réussite = $tentative->date_soumission;
			}
		}
		$this->tentatives[$i ?? count($this->tentatives)] = $tentative;
	}

	private function mettre_à_jour_dates_et_état()
	{
		$tentatives = $this->tentatives;

		$this->etat = empty($this->tentatives) ? Question::ETAT_DEBUT : Question::ETAT_NONREUSSI;
		$this->date_modification = null;
		$this->date_réussite = null;
		$this->tentatives = [];

		foreach ($tentatives as $i => $tentative) {
			$this->ajouter_tentative($tentative, $i);
		}
	}
}

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

//use InvalidArgumentException;

class Profil
{
	public string $username;
	public string $courriel;
	public string $nomComplet;
    public string $prenom;
    public string $nom;
	public string $biographie;
	public $listeConaissances;
	public $listAccomplissements;
    public string $lienGitLab;

	public function __construct(
		string $username,
		string $courriel = "",
        string $nomComplet = "",
        string $prenom = "",
        string $nom = "",
        string $biographie = "",
		$listeConaissances = [],
		$listAccomplissements = [],
		string $lienGitLab = ""
	) {
		$this->username = $username;
		$this->courriel = $courriel;
		$this->nomComplet = $nomComplet;
		$this->prenom = $prenom;
		$this->nom = $nom;
		$this->biographie = $biographie;
		$this->listeConaissances = $préfélisteConaissancesrences;
		$this->listAccomplissements = $listAccomplissements;
        $this->lienGitLab = $lienGitLab;
	}
}
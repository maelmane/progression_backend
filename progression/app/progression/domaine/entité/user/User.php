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

namespace progression\domaine\entité\user;

use InvalidArgumentException;

class User
{
	public string $username;
	public string|null $courriel;
	public État $état = État::INACTIF;
	public Rôle $rôle = Rôle::NORMAL;
	public $avancements;
	public $clés;
	public string $préférences;
	public int $date_inscription;
    public string $nom;
    public string $prénom;
    public string $nom_complet;
    public string $pseudo;
    public string $biographie;
    public Occupation $occupation = Occupation::AUTRE;
    public string $avatar;


	public function __construct(
		string $username,
		int $date_inscription,
		string|null $courriel = null,
		État $état = État::INACTIF,
		Rôle $rôle = Rôle::NORMAL,
		$avancements = [],
		$clés = [],
		string $préférences = "",
		string $nom = "",
		string $prénom = "",
		string $nom_complet = "",
		string $pseudo = "",
		string $biographie = "",
		Occupation $occupation = Occupation::AUTRE,
		string $avatar = "",
	) {
		$this->username = $username;
		$this->courriel = $courriel;
		$this->état = $état;
		$this->rôle = $rôle;
		$this->avancements = $avancements;
		$this->clés = $clés;
		$this->préférences = $préférences;
		$this->date_inscription = $date_inscription;
		$this->nom = $nom;
		$this->prénom = $prénom;
		$this->nom_complet = $nom_complet;
		$this->pseudo = $pseudo;
		$this->biographie = $biographie;
		$this->occupation = $occupation;
		$this->avatar = $avatar;
	}

}

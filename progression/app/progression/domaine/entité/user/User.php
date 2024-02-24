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
    public Occupation $occupation = Occupation::ETUDIANT;
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

	public function setCourriel(string $courriel): void
	{
		// Vérifier si l'adresse email est vide
		if (empty($courriel)) {
			throw new InvalidArgumentException("L'adresse courriel ne peut pas être vide");
		}
		// Vérifier si l'adresse est valide
		if (!filter_var($courriel, FILTER_VALIDATE_EMAIL)) {
			throw new InvalidArgumentException("L'adresse courriel n'est pas valide");
		}

		// Si l'adresse e-mail est valide, mettre à jour l'attribut courriel
		$this->courriel = $courriel;
	}

	public function setPseudo(string $pseudo): void
	{
		if (empty($pseudo)) {
			throw new InvalidArgumentException("Le pseudo ne peut pas être vide");
		}

		$this->pseudo = $pseudo;
	}

	public function setBiographie(string $biographie): void
	{
		if (strlen($biographie) > 1000) {
			throw new InvalidArgumentException("La biographie ne peut pas dépasser 1000 caractères");
		}

		$this->biographie = $biographie;
	}

	public function setAvatar(string $avatar): void
	{
		if (!filter_var($avatar, FILTER_VALIDATE_URL)) {
			throw new InvalidArgumentException("L'URL de l'avatar n'est pas valide");
		}

		$this->avatar = $avatar;
	}
}

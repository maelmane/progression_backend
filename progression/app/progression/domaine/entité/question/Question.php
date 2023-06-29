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

namespace progression\domaine\entité\question;
use progression\domaine\entité\Test;

class Question
{
	public $niveau = null;
	public $titre = null;
	public string|null $description = null;
	public string|null $objectif = null;
	public $enonce = null;
	public $auteur = null;
	public $licence = null;
	public string|null $feedback_pos = null;
	public string|null $feedback_neg = null;
	public string|null $feedback_err = null;
	/**
	 * @var array<Test> $tests
	 */
	public array $tests = [];

	/**
	 * @param string|array<mixed>|null $enonce
	 * @param array<Test> $tests
	 */
	public function __construct(
		$niveau = null,
		$titre = null,
		string|null $objectif = null,
		string|array|null $enonce = null,
		$auteur = null,
		$licence = null,
		string $feedback_pos = null,
		string $feedback_neg = null,
		string $feedback_err = null,
		$tests = [],
		string|null $description = null,
	) {
		$this->niveau = $niveau;
		$this->titre = $titre;
		$this->objectif = $objectif;
		$this->enonce = $enonce;
		$this->auteur = $auteur;
		$this->licence = $licence;
		$this->feedback_pos = $feedback_pos;
		$this->feedback_neg = $feedback_neg;
		$this->feedback_err = $feedback_err;
		$this->tests = $tests;
		$this->description = $description;
	}
}

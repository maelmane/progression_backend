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

class QuestionProg extends Question
{
	public $exécutables = [];
	public $tests = [];

	public function __construct(
		$niveau = null,
		$titre = null,
		$description = null,
		$enonce = null,
		$auteur = null,
		$licence = null,
		$feedback_pos = null,
		$feedback_neg = null,
		$feedback_err = null,
		$exécutables = [],
		$tests = []
	) {
		parent::__construct(
			$niveau,
			$titre,
			$description,
			$enonce,
			$auteur,
			$licence,
			$feedback_pos,
			$feedback_neg,
			$feedback_err,
		);
		$this->exécutables = $exécutables;
		$this->tests = $tests;
	}
}

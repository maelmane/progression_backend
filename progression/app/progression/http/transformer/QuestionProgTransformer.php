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

namespace progression\http\transformer;

use progression\util\Encodage;

class QuestionProgTransformer extends QuestionTransformer
{
	protected array $availableIncludes = ["tests", "ebauches"];

	public function transform($question)
	{
		$data_out = array_merge(parent::transform($question), [
			"sous-type" => "questionProg",
		]);

		return $data_out;
	}

	public function includeTests($question)
	{
        $id_parent = $question->id;
        
		foreach ($question->tests as $test) {
			$test->links = [
				"related" => $_ENV["APP_URL"] . "question/{$id_parent}",
			];
		}

		return $this->collection($question->tests, new TestTransformer($id_parent), "test");
	}

	public function includeEbauches($question)
	{
        $id_parent = $question->id;

		foreach ($question->exécutables as $ébauche) {
			$ébauche->links = [
				"related" => $_ENV["APP_URL"] . "question/{$id_parent}",
			];
		}

		return $this->collection($question->exécutables, new ÉbaucheTransformer($id_parent), "ebauche");
	}
}

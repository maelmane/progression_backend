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

	public function transform($data_in)
	{
		$data_out = array_merge(parent::transform($data_in), [
			"sous-type" => "questionProg",
		]);

		return $data_out;
	}

	public function includeTests($data_in)
	{
		$question = $data_in["question"];

		foreach ($question->tests as $i => $test) {
			$test->numéro = $i;
			$test->links = [
				"related" => $_ENV["APP_URL"] . "question/" . Encodage::base64_encode_url($question->uri),
			];
		}

		return $this->collection($question->tests, new TestTransformer(Encodage::base64_encode_url($question->uri)), "test");
	}

	public function includeEbauches($data_in)
	{
		$question = $data_in["question"];

		foreach ($question->exécutables as $ébauche) {
			$ébauche->links = [
				"related" => $_ENV["APP_URL"] . "question/" . Encodage::base64_encode_url($question->uri),
			];
		}

		return $this->collection($question->exécutables, new ÉbaucheTransformer(Encodage::base64_encode_url($question->uri)), "ebauche");
	}
}

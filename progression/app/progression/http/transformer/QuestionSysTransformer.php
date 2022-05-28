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

class QuestionSysTransformer extends QuestionTransformer
{
	protected array $availableIncludes = ["tests"];

	public function transform($data_in)
	{
		$question = $data_in["question"];

		$data_out = array_merge(parent::transform($data_in), [
			"sous-type" => "questionSys",
			"image" => $question->image,
			"utilisateur" => $question->utilisateur,
			"solution" => $question->solution,
		]);

		return $data_out;
	}

	public function includeTests($data_in)
	{
		$question = $data_in["question"];

        $id_parent = $this->id . "/" . Encodage::base64_encode_url($question->uri);
		foreach ($question->tests as $i => $test) {
			$test->id = $i;
			$test->links = [
				"related" => $id_parent,
			];
		}

		return $this->collection($question->tests, new TestSysTransformer($id_parent), "test");
	}
}

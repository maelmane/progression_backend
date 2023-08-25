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
use progression\http\transformer\dto\QuestionDTO;

class QuestionSysTransformer extends QuestionTransformer
{
	protected array $availableIncludes = ["tests"];

	public function transform(QuestionDTO $data_in)
	{
		$id = $data_in->id;
		$question = $data_in->objet;

		$data_out = array_merge(parent::transform($data_in), [
			"sous_type" => "questionSys",
			"image" => $question->image ?? "",
			"utilisateur" => $question->utilisateur ?? "",
			"solution" => $question->solution ?? "",
		]);

		return $data_out;
	}

	public function includeTests(QuestionDTO $data_in)
	{
		$id = $data_in->id;

		return $this->collection($data_in->tests, new TestSysTransformer(), "test");
	}
}

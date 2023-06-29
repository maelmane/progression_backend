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

use progression\http\transformer\dto\QuestionDTO;
use progression\util\Encodage;

class QuestionTransformer extends BaseTransformer
{
	public $type = "question";

	public function transform(QuestionDTO $data_in)
	{
		$id = $data_in->id;
		$question = $data_in->objet;
		$liens = $data_in->liens;

		$data_out = [
			"id" => $id,
			"niveau" => $question->niveau ?? "",
			"titre" => $question->titre ?? "",
			"objectif" => $question->objectif ?? "",
			"description" => $question->description ?? "",
			"énoncé" => $question->enonce ?? "",
			"auteur" => $question->auteur ?? "",
			"licence" => $question->licence ?? "",
			"links" => $liens,
		];

		return $data_out;
	}
}

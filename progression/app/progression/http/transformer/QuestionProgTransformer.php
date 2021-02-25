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


class QuestionProgTransformer extends QuestionTransformer
{
	public $type = "QuestionProg";

	protected $availableIncludes = ['tests', 'ébauches'];

	public function includeTests($data_in)
	{
        $question = $data_in['question'];
        
		foreach ($question->tests as $i => $test) {
			$test->numéro = $i;
			$test->id = base64_encode($question->chemin) . "/$i";
			$test->links = [
				"related" =>
					$_ENV['APP_URL'] .
					"question/" .
					base64_encode($question->chemin),
			];
		}

		return $this->collection(
			$question->tests,
			new TestTransformer(),
			"Test"
		);

	}

	//Doit être en minuscules à cause de l'accent (É n'est pas transformé en é)
	public function includeébauches($data_in)
	{
		$question = $data_in['question'];

		foreach ($question->exécutables as $ébauche) {
            $ébauche->id = base64_encode($question->chemin) . "/{$ébauche->lang}";
			$ébauche->links = [
				"related" =>
					$_ENV['APP_URL'] .
					"question/" .
					base64_encode($question->chemin),
			];
		}
        
        return $this->collection(
			$question->exécutables,
			new ÉbaucheTransformer(),
			"Ébauche"
		);

	}
}

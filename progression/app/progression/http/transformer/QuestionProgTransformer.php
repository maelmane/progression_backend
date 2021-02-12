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

use progression\domaine\entité\Question;
use progression\domaine\entité\Test;
use League\Fractal;

class QuestionProgTransformer extends Fractal\TransformerAbstract {

    public $type = "QuestionProg";

    protected $availableIncludes = ['Tests'];    

	public function transform(Question|null $question)
	{
        if ($question == null ) {
            $data = [ null ];
        }
        else {
            $data = [
                'id' => $question->id,
                'titre' => $question->titre,
                'description' => $question->description,
                'énoncé' => $question->enonce,
                'type_de_question' => $this->type,
                'links'   => [
                    [
                        'rel' => 'self',
                        'self' => 'https://progression.dti.crosemont.quebec/api/v1/question/'
                    ]
                ]
            ];
        }

        return $data;
    }

    public function includeTests( $question ){
        return $this->collection($question->tests, new TestTransformer, "Test");
    }
}

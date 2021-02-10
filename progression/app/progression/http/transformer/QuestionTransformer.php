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
use League\Fractal;

class QuestionTransformer extends Fractal\TransformerAbstract
{
	public function transform(Question $question)
	{
        return [
            'catégorieID' => $question->serieID,
            'actif' => $question->actif,
            'numero' => $question->numero,
            'titre' => $question->titre,
            'description' => $question->description,
            'enonce' => $question->enonce,
            'feedback_pos' => $question->feedback_pos,
            'feedback_neg' => $question->feedback_neg,
            'etat' => $question->etat,
            'code_validation' => $question->code_validation,
            'links'   => [
                [
                    'rel' => 'self',
                    'uri' => '/question/'.$question->id
                ]
            ]                
        ];
    }
}

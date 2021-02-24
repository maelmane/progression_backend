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

use progression\domaine\entité\QuestionProg;
use progression\domaine\entité\Exécutable;
use PHPUnit\Framework\TestCase;

final class ÉbaucheTransformerTests extends TestCase
{
    public function test_étant_donné_une_questionprog_instanciée_avec_des_valeurs_lorsquon_récupère_le_transformer_ébauche_on_obtient_la_solution_selon_un_langage()
    {
        $_ENV['APP_URL'] = 'https://example.com/';

        $question = new QuestionProg();
        $question->chemin = "prog1/les_fonctions/appeler_une_fonction_paramétrée";
        $question->exécutables["python"] = new Exécutable('#+VISIBLE\ndef salutation():\n    print( "Bonjour le monde!" )\n#+TODO\n\nprint( 0 )\n', "python");
        $langage = "python";

        $résultat_attendu = [
            "id" =>
            "cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/python",
            "langage" => "python",
            "code" => '#+VISIBLE\ndef salutation():\n    print( "Bonjour le monde!" )\n#+TODO\n\nprint( 0 )\n',
            'links' => [
                'self' =>
                'https://example.com/solution/cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/python',
                'question' =>
                'https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=',
            ],
        ];

        $résultat_obtenu = (new ÉbaucheTransformer())->transform([
            "question" => $question,
            "langage" => $langage,
        ]);
        $this->assertEquals($résultat_attendu, $résultat_obtenu);
    }

    public function test_étant_donné_le_paramètre_de_transform_instanciée_null_lorsquon_récupère_le_transformer_ébauche_on_obtient_un_tableau_null()
    {
        $résultat_attendu = [null];

        $résultat_obtenu = (new ÉbaucheTransformer())->transform(null);
        $this->assertEquals($résultat_attendu, $résultat_obtenu);
    }
}

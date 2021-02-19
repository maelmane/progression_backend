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
use PHPUnit\Framework\TestCase;

final class SolutionTransformerTests extends TestCase
{
    public function test_étant_donné_une_questionprog_instanciée_avec_des_valeurs_lorsquon_récupère_le_transformer_solution_on_obtient_la_solution_selon_un_langage()
    {
        $_ENV['APP_URL'] = 'https://example.com/';
        $langage = "python";

        $question = new QuestionProg();
        $question->nom = "appeler_une_fonction_paramétrée";
        $question->chemin =
            "prog1/les_fonctions/appeler_une_fonction_paramétrée";
        $question->titre = "Appeler une fonction paramétrée";
        $question->description =
            "Appel d\'une fonction existante recevant un paramètre";
        $question->enonce =
            "La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";
        
        $résultat = [
            "id" =>
                "avancement/date_soumission (???)",
            "langage" => "python",
            "code" => '#+VISIBLE\ndef salutation():\n    print( "Bonjour le monde!" )\n#+TODO\n\nprint( 0 )\n',
            'links' => [
                'self' =>
                    'https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=/?langage=python',
                'question' =>
                    'https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=',
            ],
        ];

        $item = (new SolutionTransformer())->transform([
            "question" => $question,
            "langage" => $langage,
        ]);
        $this->assertEquals($résultat, $item);
    }

    public function test_étant_donné_le_paramètre_de_transform_instanciée_null_lorsquon_récupère_le_transformer_solution_on_obtient_un_tableau_null()
    {
        $résultat = [null];

        $item = (new SolutionTransformer())->transform(null);
        $this->assertEquals($résultat, $item);
    }
}

?>
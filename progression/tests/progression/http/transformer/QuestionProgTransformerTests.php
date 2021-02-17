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
use progression\domaine\entité\Test;
use PHPUnit\Framework\TestCase;

final class QuestionProgTransformerTests extends TestCase
{
    public function test_étant_donné_une_questionprog_instanciée_avec_des_valeurs_lorsquon_récupère_son_transformer_on_obtient_un_array_d_objets_identique_avec_les_liens()
    {
        $_ENV['APP_URL'] = 'https://example.com/';
        $username = "jdoe";

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
                "cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=",
            "nom" => "appeler_une_fonction_paramétrée",
            "titre" => "Appeler une fonction paramétrée",
            "description" =>
                "Appel d\'une fonction existante recevant un paramètre",
            "énoncé" =>
                "La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.",
            'links' => [
                'self' =>
                    'https://example.com/question/cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=',
                'avancement' =>
                    'https://example.com/avancement/jdoe/cHJvZzEvbGVzX2ZvbmN0aW9ucy9hcHBlbGVyX3VuZV9mb25jdGlvbl9wYXJhbcOpdHLDqWU=',
                'catégorie' =>
                    'https://example.com/catégorie/cHJvZzEvbGVzX2ZvbmN0aW9ucw==',
            ],
        ];

        $item = (new QuestionProgTransformer())->transform([
            "question" => $question,
            "username" => $username,
        ]);
        $this->assertEquals($résultat, $item);
    }
}

?>

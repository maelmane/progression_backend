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

namespace progression\dao;

use progression\domaine\entité\Question;

class MockQuestionDAO extends QuestionDAO
{

    public function get_question($chemin)
    {
        $question = new Question();
        $question->chemin = $chemin;

        $this->load($question);
        return $question;
    }

    protected function load($objet)
    {
        if ($objet->chemin == "prog1/les_fonctions/appeler_une_fonction") {
            $objet->id = 1;
            $objet->nom = "appeler_une_fonction";
            $objet->chemin = "prog1/les_fonctions/appeler_une_fonction";
            $objet->titre = "Appeler une fonction paramétrée";
            $objet->description = "Appel d'une fonction existante recevant un paramètre";
            $objet->enonce = "La fonction `salutations` affiche une salution autant de fois que la valeur reçue en paramètre. Utilisez-la pour faire afficher «Bonjour le monde!» autant de fois que le nombre reçu en entrée.";
        }
        if ($objet->chemin == "prog1/les_classes/les_constructeurs") {
            $objet->id = 2;
            $objet->nom = "les_constructeurs";
            $objet->chemin = "prog1/les_classes/les_constructeurs";
            $objet->titre = "Créer un construteur de classe";
            $objet->description = "Construteur avec et sans paramètre";
            $objet->enonce = "Faire un constructeur avec et sans paramètre pour la class X";
        }
    }
}

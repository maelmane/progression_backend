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

use progression\domaine\entité\{QuestionProg, Exécutable, Test};;

class MockQuestionProgDAO extends MockQuestionDAO
{

    public function get_question($chemin)
    {
        $questionProg = new QuestionProg();
        $questionProg->chemin = $chemin;

        $this->load($questionProg);
        return $questionProg;
    }

    protected function load($objet)
    {
        parent::load($objet);

        $objet->exécutables = $this->load_exécutables($objet->id);
        $objet->tests = $this->load_tests($objet->id);
    }

    private function load_exécutables($id)
    {
        if ($id == 1) {
            $exécutables["java"] = new Exécutable("return nb1 + nb2;", "java");
            $exécutables["python"] = new Exécutable("return nb1 + nb2", "python");
        }
        if ($id == 2) {
            $exécutables["c++"] = new Exécutable("public X(int i)", "c++");
            $exécutables["c#"] = new Exécutable("public X(int i)", "c#");
        }
        return $exécutables;
    }

    private function load_tests($id)
    {
        if ($id == 1) {
            $tests[] = new Test("appeler_une_fonction", "21\n21\n", "42");
            $tests[] = new Test("appeler_une_fonction", "700\n77\n", "777");
        }
        if ($id == 2) {
            $tests[] = new Test("les_constructeurs", "", "error de compilation");
            $tests[] = new Test("les_constructeurs", "test", "object",);
        }
        return $tests;
    }
}

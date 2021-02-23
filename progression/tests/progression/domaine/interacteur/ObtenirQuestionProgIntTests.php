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

namespace progression\domaine\interacteur;

use PHPUnit\Framework\TestCase;
use \Mockery as m;
use progression\domaine\entité\QuestionProg;

final class ObtenirQuestionProgIntTests extends TestCase
{
    public function test_étant_donné_une_questionprog_trouvée_par_son_chemin_on_obtient_une_questionprog_correspondante()
    {
        $résultat = new QuestionProg();
        $résultat->chemin = 'prog1/les_fonctions/appeler_une_fonction';
        
        $mockQuestionProgDao = m::mock( 'progression\dao\QuestionProgDAO' );
        $mockQuestionProgDao->shouldReceive( 'get_question' )
            ->with( $résultat->chemin )
            ->andReturn( $résultat );

        $mockFactory = m::mock( 'progression\dao\DAOFactory' );
        $mockFactory->shouldReceive( 'get_question_prog_dao' )
            ->andReturn( $mockQuestionProgDao );
        
        $interacteur = new ObtenirQuestionProgInt( $mockFactory );
        $résultatTest = $interacteur->get_question( $résultat->chemin );

        $this->assertEquals( $résultat, $résultatTest );
    }

    public function test_étant_donné_une_questionprog_non_trouvée_par_son_chemin_on_obtient_null()
    {
        $résultat = null;
        $chemin = 'test/de/chemin/non/valide';
        
        $mockQuestionProgDao = m::mock( 'progression\dao\QuestionProgDAO' );
        $mockQuestionProgDao->shouldReceive( 'get_question' )
            ->with( $chemin )
            ->andReturn( null );

        $mockFactory = m::mock( 'progression\dao\DAOFactory' );
        $mockFactory->shouldReceive( 'get_question_prog_dao' )
            ->andReturn( $mockQuestionProgDao );
        
        $interacteur = new ObtenirQuestionProgInt( $mockFactory );
        $résultatTest = $interacteur->get_question( $chemin );

        $this->assertEquals( $résultat, $résultatTest );
    }
}

?>
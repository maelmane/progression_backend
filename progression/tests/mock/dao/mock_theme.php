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

use progression\domaine\entité\Thème;

class MockThèmeDAO extends ThèmeDAO{


    static function get_thèmes($inactif = false){
        return [MockThèmeDAO::get_thème(0), MockThèmeDAO::get_thème(1), MockThèmeDAO::get_thème(2)];
    }

    static function get_thème($id)
    {
        $thème = new Thème($id);

        MockThèmeDAO::load($thème);

        return $thème;
    }

    protected static function load($objet){
        $objet->titre = "Thème factice #" . $objet->id;
        $objet->description = "Ceci est le thème factice numéro " . $objet->id;
        $objet->séries_id = [ 8, 22, 43 ];
    }
    
    static function get_nb_questions_actives($id){
        return 42;
    }

    function get_avancement($thème_id, $user_id){
        return 8;
    }

}

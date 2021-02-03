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
?><?php

namespace progression\http\controleurs;
use progression\dao\mock\DAOFactory;

class UtilisateurControleur extends Controller
{
    function __construct()
    {
    }

    public function getUser($nomUtilisateur){
        $dao_factory = new DAOFactory();
        $userdao = $dao_factory->get_user_dao();
        $user = $userdao->trouver_par_nomusager($nomUtilisateur);
        
        return response()->json($user);
    }

    public function getUserP(){
        $dao_factory = new DAOFactory();
        $userdao = $dao_factory->get_user_dao();
        $user = $userdao->trouver_par_nomusager("bob");
        
        return response()->json($user);
    }
}

?>

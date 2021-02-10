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

namespace progression\http\contrôleur;

use Illuminate\Http\Request;
use progression\domaine\entité\User;
use progression\dao\DAOFactory;
use progression\http\transformer\UserTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;

class UserCtl extends Contrôleur
{
    public function get(Request $request){
        $username = $request->input("username");

        if ($username != null && $username != "" ) {
            $dao_factory = new DAOFactory();
            $user_dao = $dao_factory->get_user_dao();
            $user = $user_dao->trouver_par_nomusager($username);
        }
        else{
            $user = null;
        }

        $resource = new Item($user, new UserTransformer);
        $fractal = new Manager();
        $réponse = $fractal->createData($resource);

        return $this->réponseJson($réponse, 200);

    }
}

?>

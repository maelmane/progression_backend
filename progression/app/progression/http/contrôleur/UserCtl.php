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

use Exception;
use Illuminate\Http\Request;
use progression\domaine\interacteur\ObtenirUserInt;
use progression\dao\DAOFactory;
use progression\http\transformer\UserTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Item;
use Illuminate\Support\Facades\Log;


class UserCtl extends Contrôleur
{
    public function get(Request $request, $username=null){
        $userInt = new ObtenirUserInt(new DAOFactory());
        $user = null;
        
        if ($username == null ) {
            $username = $request->username;
        }

        if ($username != null && $username != "" ) {
            $user = $userInt->get_user_par_nomusager($username);
        }

        if ($user != null ) {
            $resource = new Item($user, new UserTransformer);
            $fractal = new Manager();
            $réponse = $fractal->createData($resource);
            Log::info("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
            return $this->réponse_json($réponse, 200);

        } else {
            Log::warning("(" . $request->ip() . ") - " . $request->method() . " " . $request->path() . "(" . __CLASS__ . ")");
            return $this->réponse_json(['message' => 'Utilisateur non trouvé.'], 404);
        }
    }
}

?>

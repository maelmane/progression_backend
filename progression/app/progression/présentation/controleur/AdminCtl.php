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


namespace progression\présentation\controleur;

use progression\domaine\interacteur{ObtenirUserInt, MettreÀJourThèmesInt};
use progression\domaine\entité\User;

class AdminCtl extends Controleur
{
    function __construct($source, $user_id)
    {
        parent::__construct($source, $user_id);
    }

    function get_page_infos()
    {
        $user = (new ObtenirUserInt($this->_source))->get_user($this->_user_id);
        if ($user->role == User::ROLE_ADMIN) {
            if (isset($_REQUEST["submit"])) {
                $url = $_REQUEST["url"];
                $username = $_REQUEST["username"];
                $password = $_REQUEST["password"];
                if (
                    !(new MettreÀJourThèmesInt())->exécuter(
                        $url,
                        $username,
                        $password
                    )
                ) {
                    $this->_erreurs += ["Erreur d'importation"];
                }
            }

            return array_merge(parent::get_page_infos(), [
                "titre" => "Page d'administration",
                "template" => "admin",
            ]);
        } else {
            return (new AccueilCtl(
                $this->_source,
                $this->_user_id
            ))->get_page_infos();
        }
    }
}

?>

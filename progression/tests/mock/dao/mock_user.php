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

use progression\domaine\entité\User;

class MockUserDAO extends UserDAO{
    public function trouver_par_nomusager($username){
        if ($username == "admin"){
            return MockUserDAO::get_user(0);
        }
        else{
            return MockUserDAO::get_user(42);
        }
    }

    public function get_user($user_id)
    {
        $user = new User($user_id);
        UserDAO::load($user);

        return $user;
    }

    protected function load($objet){
        if ($objet->id == 0){
            $objet->username = "admin";
            $objet->role = User::ROLE_ADMIN;
        }
        if ($objet->id == 42){
            $objet->username = "bob";
            $objet->role = User::ROLE_NORMAL;
        }
    }
}

?>

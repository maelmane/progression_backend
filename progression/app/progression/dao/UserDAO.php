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

class UserDAO extends EntitéDAO
{

    public function get_user($username)
    {
        $user = new User($username);
        $this->load($user);

        return $user->username ? $user : null;
    }

    protected function load($objet)
    {
        $query = $this->conn->prepare(
            'SELECT user_id, role FROM users WHERE user_id = ? '
        );
        $query->bind_param("i", $objet->username);
        $query->execute();

        $query->bind_result($objet->username, $objet->role);
        $res = $query->fetch();
        $query->close();
    }

    public function save($objet)
    {
        $query = $this->conn->prepare(
            'INSERT INTO users( user_id, role ) VALUES ( ?, ? ) ON DUPLICATE KEY UPDATE role=VALUES( role )'
        );
        $query->bind_param("si", $objet->username, $objet->role);
        $query->execute();
        $query->close();

        return $this->trouver_par_nomusager($objet->username);
    }
}
?>

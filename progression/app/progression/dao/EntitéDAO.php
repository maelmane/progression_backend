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

class ConnexionException extends \Exception{};

class EntitéDAO
{
    static $conn=null;

    function db_init()
    {
        if ( ! EntitéDAO::$conn ){
            create_connection();
            if ( mysqli_connect_errno() != 0 ){
                throw new ConnexionException( mysqli_connect_error() . "(".mysqli_connect_errno().")" );
            }
        }
    }

    function create_connection()
    {
        EntitéDAO::$conn = new \mysqli(
            $_ENV["SERVERNAME"],
            $_ENV["USERNAME"],
            $_ENV["PASSWORD"],
            $_ENV["DBNAME"]
        );
        EntitéDAO::$conn->set_charset("utf8");
    }

    private function __construct(){
        db_init();
        $this->conn = EntitéDAO::$conn;
    }

}

?>

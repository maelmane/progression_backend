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

class EntiteDAO
{
    static $conn;
}

require_once "config.php";

function db_init()
{
    if (!isset($GLOBALS["conn"])) {
        create_connection();
        set_errors();
    }
}

function create_connection()
{
    $GLOBALS["conn"] = new mysqli(
        $GLOBALS["config"]["servername"],
        $GLOBALS["config"]["username"],
        $GLOBALS["config"]["password"],
        $GLOBALS["config"]["dbname"]
    );
    $GLOBALS["conn"]->set_charset("utf8");
}

function set_errors()
{
    $GLOBALS["errno"] = mysqli_connect_errno();
    $GLOBALS["error"] = mysqli_connect_error();
}

db_init();
EntiteDAO::$conn = $GLOBALS["conn"];

?>

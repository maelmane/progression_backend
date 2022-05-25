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

class ConnexionException extends \Exception
{
}

class EntitéDAO
{
	private static $conn = null;
	protected $source = null;

	public function __construct($source = null)
	{
		if ($source == null) {
			$this->source = DAOFactory::getInstance();
		} else {
			$this->source = $source;
		}
	}

	public static function get_connexion()
	{
		if (EntitéDAO::$conn == null) {
			EntitéDAO::create_connection();
			if (mysqli_connect_errno() != 0) {
				throw new ConnexionException(mysqli_connect_error() . "(" . mysqli_connect_errno() . ")");
			}
		}

		return EntitéDAO::$conn;
	}

	private static function create_connection()
	{
		// Limite les exceptions lancées aux erreurs rapportées par MySQL
		mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
		EntitéDAO::$conn = new \mysqli(
			$_ENV["DB_SERVERNAME"],
			$_ENV["DB_USERNAME"],
			$_ENV["DB_PASSWORD"],
			$_ENV["DB_DBNAME"],
		);
		EntitéDAO::$conn->set_charset("utf8mb4");
	}

	protected static function stmt_bind_assoc(&$stmt, &$out)
	{
		$data = mysqli_stmt_result_metadata($stmt);
		$fields = [];
		$out = [];

		$fields[0] = $stmt;
		$count = 1;

		while ($field = mysqli_fetch_field($data)) {
			$fields[$count] = &$out[$field->name];
			$count++;
		}
		call_user_func_array("mysqli_stmt_bind_result", $fields);
	}
}

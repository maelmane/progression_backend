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

class MissingException extends Exception{}

// chargement des fichiers automatique
spl_autoload_register(function ($class_name) {
	$class = str_replace('\\', '/', $class_name) . '.php';

    if (file_exists(__DIR__ . "/" . $class)){
        require $class;
    }
    else {
        throw new MissingException("Impossible de charger " . __DIR__ . "/" . $class);
    }
    
});


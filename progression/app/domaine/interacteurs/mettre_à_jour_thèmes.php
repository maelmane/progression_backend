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


require_once __DIR__ . '/interacteur.php';

class MettreÀJourThèmesInt extends Interacteur
{
    function __construct()
    {
    }

    function exécuter($url, $username, $password)
    {
        exec(
            "git clone " .
                escapeshellarg(
                    str_replace(
                        "://",
                        "://$username:" .
                            str_replace("@", "\@", $password) .
                            "@",
                        $url
                    )
                ) .
                " /tmp/update_progression",
            $output,
            $ret
        );

        if ($ret == 0) {
            exec(
                "importer.py /tmp/update_progression/thèmes mysql://" .
                    $GLOBALS['config']['username'] .
                    ":" .
                    $GLOBALS['config']['password'] .
                    "@" .
                    $GLOBALS['config']['servername'] .
                    "/" .
                    $GLOBALS['config']['dbname'],
                $output,
                $ret
            );
        }

        exec("rm -rf /tmp/update_progression");

        return !$ret;
    }
}

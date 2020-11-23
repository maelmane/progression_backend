<?php

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

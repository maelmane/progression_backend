<?php

class EntiteDAO{

    static $conn;

}

require_once(__DIR__."/../config.php");

function db_init(){
    if(!isset($GLOBALS["conn"]))
    {
        create_connection();
        set_errors();
    }
}

function create_connection(){
    $GLOBALS["conn"] = new mysqli($GLOBALS["config"]["servername"],
                                  $GLOBALS["config"]["username"],
                                  $GLOBALS["config"]["password"],
                                  $GLOBALS["config"]["dbname"]);
    $GLOBALS["conn"]->set_charset("utf8");
}

function set_errors(){
    $GLOBALS["errno"]=mysqli_connect_errno();
    $GLOBALS["error"]=mysqli_connect_error();
}

db_init();
EntiteDAO::$conn=$GLOBALS["conn"];

?>

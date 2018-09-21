<?php

function format_array($arr, $longueur=99){
    $res="";
    foreach($arr as $a){
        $res=$res.strval($a).", ";
    }
    $res="[".substr($res,0,sizeof($res)-3)."]";
    $res=preg_replace('/(.{1,'.strval($longueur-3).'}\W)/', "$1 \\\n", $res);

    return substr($res,0,sizeof($res)-4);
}

?>


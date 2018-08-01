<?php

function get_lang_defaut(){
    $langid=QuestionProg::PYTHON3;
    if (isset($_POST['langid'])&&$_POST['langid']!="") $langid=$_POST['langid'];

    return $langid;
}

function get_code(){
    $code="";
    if (isset($_POST['incode'])&&$_POST['incode']!="") $code=$_POST['incode'];

    return $code;
}

function get_params(){
    $params="";
    if(isset($_POST['params'])&&$_POST['params']!="") $params=$_POST['params'];

    return $params;
}

function get_stdin(){
    $stdin="";
    if(isset($_POST['stdin'])&&$_POST['stdin']!="") $stdin=trim($_POST['stdin']);

    return $stdin;
}

?>

<?php

require_once('question.php');

class QuestionProg extends Question{

    const PYTHON3=1;
    const CPP=8;
    const JAVA=10;    

    //Données
    public $lang;
    public $setup;
    public $pre_exec;
    public $pre_code;
    public $incode;
    public $post_code;
    public $solution;
    public $params;
    public $stdin;

}

?>

<?php

class Test
{
    public function __construct($nom, $stdin, $solution, $params = null, $fbp=null, $fbn=null)
    {
        $this->nom = $nom;
        $this->stdin = $stdin;
        $this->solution = $solution;
        $this->params = $params;
        $this->feedback_pos = $fbp;
        $this->feedback_neg = $fbn;
    }

    public $nom;
    public $stdin;
    public $params = null;
    public $solution;
    public $feedback_pos;
    public $feedback_neg;

}

?>

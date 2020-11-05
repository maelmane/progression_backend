<?php

class Test
{
    public function __construct($nom, $stdin, $solution, $params = null)
    {
        $this->nom = $nom;
        $this->stdin = $stdin;
        $this->solution = $solution;
        $this->params = $params;
    }

    public $nom;
    public $stdin;
    public $params = null;
    public $solution;
}

?>

<?php

class Exécutable
{
    public function __construct($code, $lang)
    {
        $this->code = $code;
        $this->lang = $lang;
    }

    public $code;
    public $lang;
    public $code_exec;
    public $params = null;
    public $résultats = null;
}

?>

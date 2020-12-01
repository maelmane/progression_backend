<?php

class FormaterMDInt
{
    public function exécuter($md)
    {
        $parser = new Parsedown();
        return $parser->text($md);
    }
}

?>

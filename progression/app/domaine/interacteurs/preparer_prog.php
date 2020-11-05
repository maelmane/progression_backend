<?php

class PréparerProgInt
{
    public function préparer_exécutable($question, $langage, $code)
    {
        $exécutable = $question->exécutables[$langage];
        $exécutable->code = $code;
        $exécutable->code_exec = $code;

        return $exécutable;
    }
}
?>

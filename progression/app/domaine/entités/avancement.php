<?php

require_once __DIR__ . '/entite.php';

class Avancement
{
    public $user_id;
    public $question_id;
    public $etat = Question::ETAT_DEBUT;
    public $code;
    public $reponse;
    public $conteneur;

    public function __construct($question_id, $user_id)
    {
        $this->question_id = $question_id;
        $this->user_id = $user_id;
    }
}

?>

<?php

require_once __DIR__ . '/avancement.php';

class AvancementProg extends Avancement
{
	public $reponses;
    public $lang;

	public function __construct($question_id, $user_id, $reponses=null, $lang=null)
	{
        parent::__construct($question_id, $user_id);
        $this->reponses=$reponses;
        $this->lang=$lang;
	}
}

?>

<?php

class Exécutable
{
	public function __construct($code, $lang)
	{
		$this->code_utilisateur = $code;
		$this->code_exec = $code;
		$this->lang = $lang;
	}

	public $code_utilisateur;
	public $lang;
	public $code_exec;
	public $params = null;
	public $résultats = null;
}

?>

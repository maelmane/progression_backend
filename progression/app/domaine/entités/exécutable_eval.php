<?php

require_once __DIR__ . '/exécutable.php';

class ExécutableEval extends Exécutable
{
	public $langid;
	public $pre_exec;
	public $pre_code;
	public $post_code;
	public $params;
}

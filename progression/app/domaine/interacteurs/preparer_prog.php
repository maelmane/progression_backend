<?php

require_once __DIR__ . '/interacteur.php';
require_once 'domaine/entités/question_prog.php';

class PréparerProgInt extends Interacteur
{
	public function __construct()
	{
		parent::__construct(null);
	}

	public function get_exécutable(
		$question,
		$avancement,
		$params,
		$stdin,
		$incode
	) {
		$exécutable = new class {};
		$exécutable->langid = $question->lang;
		$exécutable->pre_exec = $question->pre_exec;
		$exécutable->pre_code = $question->pre_code;
		$exécutable->code = $this->get_code_utilisateur(
			$question,
			$avancement,
			$incode
		);
		$exécutable->post_code = $question->post_code;
		$exécutable->params = $this->get_params($question, $params);
		$exécutable->stdin = $this->get_stdin($question, $stdin);

		$exécutable->code_exec = $this->composer_code($exécutable);
		return $exécutable;
	}

	private function composer_code($exécutable)
	{
		//Compose le code à exécuter
		return preg_replace(
			'~\R~u',
			"\n",
			$exécutable->pre_exec .
				$exécutable->pre_code .
				"\n" .
				$exécutable->code .
				"\n" .
				$exécutable->post_code
		);
	}

	protected function get_code_utilisateur($question, $avancement, $incode)
	{
		if ($incode != null) {
			return $incode;
		} elseif ($avancement->code != null) {
			return $avancement->code;
		} elseif ($question->code != null) {
			return $question->code;
		} else {
			return "";
		}
	}

	protected function get_params($question, $paramsp)
	{
		$params = "";

		if (!is_null($question) && $question->params != "") {
			$params = $question->params;
		} elseif ($paramsp != null) {
			$params = $paramsp;
		}

		return $params;
	}

	protected function get_stdin($question, $stdinp)
	{
		$stdin = "";
		if (!is_null($question) && $question->stdin != "") {
			$stdin = $question->stdin;
		} elseif ($stdinp != null) {
			$stdin = $stdinp;
		}

		return $stdin;
	}

	protected function get_mode($langid)
	{
		if ($langid <= QuestionProg::PYTHON3) {
			return "python/python.js";
		} elseif (
			$langid == QuestionProg::CPP ||
			$langid == QuestionProg::JAVA
		) {
			return "clike/clike.js";
		}
	}
}

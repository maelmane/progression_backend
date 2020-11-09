<?php

require_once __DIR__ . '/interacteur.php';
require_once 'domaine/entités/exécutable_eval.php';
require_once 'domaine/entités/question_prog.php';
require_once 'domaine/entités/test.php';

class PréparerProgEvalInt extends Interacteur
{
	public function __construct()
	{
		parent::__construct(null);
	}

	public function get_exécutable($question, $avancement, $incode)
	{
		//Truc pour que question-setup soit évalué de la même façon partout.
		$this->seed = rand();
		srand($this->seed);

		eval($question->setup);

		$question->enonce = str_replace(
			"\r",
			"",
			eval("return " . '"' . $question->enonce . '";')
		);

		$exécutable = new Exécutable("", $question->lang);
		$exécutable->pre_exec = str_replace(
			"\r",
			"",
			eval("return " . $question->pre_exec . ";")
		);
		$exécutable->pre_code = str_replace(
			"\r",
			"",
			eval("return " . $question->pre_code . ";")
		);
		$exécutable->code_utilisateur = PréparerProgEvalInt::get_code_utilisateur(
			$question,
			$avancement,
			$incode
		);
		$exécutable->post_code = str_replace(
			"\r",
			"",
			eval("return " . $question->post_code . ";")
		);

		$exécutable->code_exec = PréparerProgEvalInt::composer_code(
			$exécutable
		);
		return $exécutable;
	}

	public function get_test($question, $params, $stdin)
	{
		srand($this->seed);
		eval($question->setup);

		$solution = str_replace(
			"\r",
			"",
			eval("return " . $question->solution . ";")
		);

		$test = new Test(
			"Validation",
			PréparerProgEvalInt::get_stdin($question, $stdin),
			$solution,
			PréparerProgEvalInt::get_params($question, $params)
		);

		return $test;
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
				$exécutable->code_utilisateur .
				"\n" .
				$exécutable->post_code
		);
	}

	protected function get_code_utilisateur($question, $avancement, $incode)
	{
		if ($incode != null) {
			return $incode;
		} elseif ($avancement->code_utilisateur != null) {
			return $avancement->code_utilisateur;
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
		srand($this->seed);
		eval($question->setup);

		$stdin = "";
		if (!is_null($question) && $question->stdin != "") {
			$stdin = str_replace(
				"\r",
				"",
				eval("return " . $question->stdin . ";")
			);
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

<?php

require_once __DIR__ . '/question_prog.php';

class QuestionProgEvalCtl extends QuestionProgCtl
{
	public function get_page_infos()
	{
		return array_merge(parent::get_page_infos(), [
			"template" => "question_prog_eval",
		]);
	}

	protected function récupérer_paramètres()
	{
		eval($this->question->setup);

		$this->question->énoncé = str_replace(
			"\r",
			"",
			eval("return \"" . $this->question->enonce . "\";")
		);
		$this->question->solution = str_replace(
			"\r",
			"",
			eval("return " . $this->question->solution . ";")
		);
		$this->question->pre_exec = str_replace(
			"\r",
			"",
			eval("return " . $this->question->pre_exec . ";")
		);
		$this->question->pre_code = str_replace(
			"\r",
			"",
			eval("return " . $this->question->pre_code . ";")
		);
		$this->question->post_code = str_replace(
			"\r",
			"",
			eval("return " . $this->question->post_code . ";")
		);

		$infos = array_merge(parent::récupérer_paramètres(), [
			"première_ligne_éditeur_precode" =>
				$this->compter_lignes($this->question->pre_exec) + 1,
			"première_ligne_éditeur_incode" =>
				$this->compter_lignes($this->question->pre_exec) +
				$this->compter_lignes($this->question->pre_code) +
				1,
		]);

		return $infos;
	}

	private function compter_lignes($texte)
	{
		if ($texte == "") {
			return 0;
		} else {
			return count(preg_split('/\n/', $texte));
		}
	}
}

?>

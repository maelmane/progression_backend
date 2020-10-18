<?php

require_once __DIR__.'/interacteur.php';
require_once 'domaine/entité/question_prog.php';

class PréparerProgInt extends Interacteur {
	
	protected function get_code(){
		$code="";

		if ( $this->incode!=null ){
			$code=$this->incode;
		}
		else{
			if( !is_null( $this->avancement ) && $this->avancement->code!='' ){
				$code=$this->avancement->code;
			}
			elseif( !is_null( $this->question )){
				$code=$this->question->incode;
			}
		}

		return $code;
	}

	protected function get_params(){
		$params="";

		if( !is_null( $this->question ) && $this->question->params!="" ){
			$params=$this->question->params;
		}
		elseif( $this->params!=null ){
			$params=$this->params;
		}

		return $params;
	}

	protected function get_stdin(){
		$stdin="";
		if( !is_null( $this->question ) && $this->question->stdin!="" ){
			$stdin=$this->question->stdin;
		}
		elseif( $this->stdin!=null ){
			$stdin=$this->stdin;
		}

		return $stdin;
	}

	protected function get_mode( $langid ){
		if( $langid<=QuestionProg::PYTHON3 ){
			return "python/python.js";
		}
		elseif( $langid==QuestionProg::CPP || $langid==QuestionProg::JAVA ){
			return "clike/clike.js";
		}
	}

}

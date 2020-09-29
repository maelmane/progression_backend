<?php

require_once('question_dao.php');
require_once('question_prog.php');

class QuestionProgDAO extends QuestionDAO{
    
    protected static function load($objet){
	parent::load($objet);
	$query=QuestionProgDAO::$conn->prepare('SELECT question_prog.lang, 
                                                theme.lang, 
                                                question_prog.setup, 
                                                question_prog.pre_exec, 
                                                question_prog.pre_code, 
                                                question_prog.in_code, 
                                                question_prog.post_code, 
                                                question_prog.solution, 
                                                question_prog.params, 
                                                question_prog.stdin
                                                FROM question 
                                                JOIN question_prog ON
                                                question.questionID=question_prog.questionID 
                                                JOIN serie ON
                                                question.serieID=serie.serieID 
                                                JOIN theme ON
                                                serie.themeID=theme.themeID
                                                WHERE question.questionID = ?');

	$query->bind_param( "i", $objet->id);
	$query->execute();
	$query->bind_result( $qlang,
			     $tlang,
			     $objet->setup,
			     $objet->pre_exec,
			     $objet->pre_code,
			     $objet->incode,
			     $objet->post_code,
			     $objet->reponse,
			     $objet->params,
			     $objet->stdin
	);
	if(is_null($query->fetch()))
	    $objet->id=null;
	if(is_null($qlang) || $qlang==-1)
	    $objet->lang=$tlang;
	else
	    $objet->lang=$qlang;
	$query->close();
    }

    public static function save($objet){
	if(!$objet->id){
	    $qid=parent::save($objet);
	    $query=QuestionProgDAO::$conn->prepare("INSERT INTO question_prog(questionID,
                                                    lang,
                                                    setup,
                                                    pre_exec,
                                                    pre_code,
                                                    in_code,
                                                    post_code,
                                                    reponse,
                                                    params,
                                                    stdin)
                                                    VALUES( $qid, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
	    $query->bind_param( "issssssss",
				$objet->lang,
				$objet->setup,
				$objet->pre_exec,
				$objet->pre_code,
				$objet->incode,
				$objet->post_code,
				$objet->reponse,
				$objet->params,
				$objet->stdin);
	    $query->execute();
	    $query->close();
	}
	else{
	    $qid=parent::save($objet);
	    $query=QuestionProgDAO::$conn->prepare("UPDATE question_prog SET lang=?,
                                                    setup=?,
                                                    pre_exec=?,
                                                    pre_code=?,
                                                    in_code=?,
                                                    post_code=?,
                                                    reponse=?,
                                                    params=?,
                                                    stdin=? 
                                                    WHERE questionID=$qid");
	    $query->bind_param( "issssssss",
				$objet->lang,
				$objet->setup,
				$objet->pre_exec,
				$objet->pre_code,
				$objet->incode,
				$objet->post_code,
				$objet->reponse,
				$objet->params,
				$objet->stdin);
	    $query->execute();
	    $query->close();

	}
	return $qid;
    }
    
}
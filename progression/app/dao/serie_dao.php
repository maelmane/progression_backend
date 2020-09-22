<?php

require_once('entite_dao.php');
require_once('question_dao.php');
require_once('serie.php');

class SerieDAO extends EntiteDAO{

    static function get_serie_par_id($id){
	$serie=new Serie($id);
	SerieDAO::load($serie);
	
	return $serie;
    }

    protected static function load($objet){
	$query=SerieDAO::$conn->prepare('SELECT serieID, actif, numero, titre, description, themeID FROM serie WHERE serieID = ?');
	$query->bind_param( "i", $objet->id);
	$query->execute();
	$query->bind_result( $objet->id, $objet->actif, $objet->numero, $objet->titre, $objet->description, $objet->themeID );
	if(is_null($query->fetch()))
	    $objet->id=null;
	$query->close();

	if(!is_null($objet->id)){
	    $objet->questions_ids=SerieDAO::get_questions_ids($objet->id);
	}
    }

    static function get_nb_questions_actives(){
	$query=SerieDAO::$conn->prepare('SELECT count(question.questionID) FROM question WHERE
                                                 question.actif = 1 AND
                                                 question.serieID = ?');
	$query->bind_param( "i", $this->id);
	$query->execute();
	$query->bind_result($res);
	$query->fetch();
	$query->close();

	return $res;        
    }

    static function get_questions_ids($id, $inactif=false){
	if($inactif){
	    $statement='SELECT question.questionID FROM question
                        WHERE question.serieID = ?
                        ORDER BY question.numero';
	}
	else{
	    $statement='SELECT question.questionID FROM question
                        WHERE question.serieID = ? AND
                        question.actif = 1
                        ORDER BY question.numero';
	}
	$query=SerieDAO::$conn->prepare($statement);
	$query->bind_param( "i", $id);
	$query->execute();
	$query->bind_result($q_id);

	$res=array();
	while($query->fetch()){
	    $res[]=$q_id;
	}
	$query->close();

	return $res;
    }
    
    static function get_questions($id, $inactif=false){
	$res=array();
	foreach(SerieDAO::get_questions_ids($id,$inactif) as $question_id){
	    $res[]=QuestionDAO::get_question_par_id($question_id);
	}
	return $res;
    }
    

    static function get_avancement($id, $user_id){
	$query=SerieDAO::$conn->prepare('SELECT count(avancement.etat) FROM avancement, question WHERE 
                                         avancement.questionID=question.questionID AND 
                                         avancement.userID= ? AND 
                                         question.serieID = ? AND
                                         question.actif = 1 AND
                                         avancement.etat='.Question::ETAT_REUSSI);

	$query->bind_param( "ii", $user_id, $id);
	$query->execute();
	$query->bind_result($res);
	$query->fetch();
	$query->close();
	return $res;
    }
}
?>

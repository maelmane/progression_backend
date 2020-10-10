<?php

require_once __DIR__.'/entite.php';
require_once __DIR__.'/theme.php';
require_once 'domaine/entités/serie.php';

class SérieDAO extends EntiteDAO{

    static function get_série($id){
		$série=new Série($id);
		SérieDAO::load($série);
		
		return $série;
    }
	
	static function get_séries_par_thème($id, $inactif=false){
		$res=array();
		foreach(SérieDAO::get_séries_ids_par_thème($id,$inactif) as $sérieid){
			$res[]=SérieDAO::get_série($sérieid);
		}
		return $res;
	}
	
	static function get_séries_ids_par_thème($id, $inactif=false){
		if($inactif){
			$query=ThèmeDAO::$conn->prepare('SELECT serieID FROM serie WHERE
	                                         themeID= ? ORDER BY numero');
		}
		else{
			$query=ThèmeDAO::$conn->prepare('SELECT serieID FROM serie WHERE
	                                         serie.actif = 1 AND
	                                         themeID= ? ORDER BY numero');
		}
		$query->bind_param( "i", $id);
		$query->execute();
		$query->bind_result($s_id);

		$res=array();
		while($query->fetch()){
			$res[]=$s_id;
		}
		$query->close();

		return $res;
	}

    protected static function load($objet){
		$query=SérieDAO::$conn->prepare('SELECT serieID, actif, numero, titre, description, themeID FROM serie WHERE serieID = ?');
		$query->bind_param( "i", $objet->id);
		$query->execute();
		$query->bind_result( $objet->id, $objet->actif, $objet->numero, $objet->titre, $objet->description, $objet->themeID );
		if(is_null($query->fetch()))
			$objet->id=null;
		$query->close();

		if(!is_null($objet->id)){
			$objet->questions_ids=SérieDAO::get_questions_ids($objet->id);
		}
    }

    static function get_nb_questions_actives($id){
		$query=SérieDAO::$conn->prepare('SELECT count(question.questionID) FROM question WHERE
                                                 question.actif = 1 AND
                                                 question.serieID = ?');
		$query->bind_param( "i", $id);
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
		$query=SérieDAO::$conn->prepare($statement);
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
    
    static function get_avancement($id, $user_id){
		$query=SérieDAO::$conn->prepare('SELECT count(avancement.etat) FROM avancement, question WHERE 
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

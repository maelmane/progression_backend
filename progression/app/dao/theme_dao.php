<?php

require_once('dao/entite_dao.php');
require_once('dao/serie_dao.php');
require_once('domaine/entités/theme.php');

class ThèmeDAO extends EntiteDAO{
    static function get_thèmes($inactif=false){
		if($inactif){
            $thème_ids=ThèmeDAO::$conn->query('SELECT themeID FROM theme WHERE themeID>0 ORDER BY ordre');
		}
		else{
            $thème_ids=ThèmeDAO::$conn->query('SELECT themeID FROM theme WHERE 
                                         actif = 1 AND
                                         themeID>0 ORDER BY ordre');
		}
		
		$thèmes=array();

		$row = $thème_id=$thème_ids->fetch_assoc();
		while( $row ){
			$thème_id=$row['themeID'];
            $thèmes[] = ThèmeDAO::get_thème($thème_id);;

			$row = $thème_id=$thème_ids->fetch_assoc();
		}
		$thème_ids->close();
		return $thèmes;
		
	}

	static function get_thème($id){
		$thème=new Thème($id);

		if(!is_null($id)){
			ThèmeDAO::load($thème);
		}

    	return $thème;
	}

	protected static function load($objet){
		$query=ThèmeDAO::$conn->prepare('SELECT themeID, actif, titre, description FROM theme WHERE themeID = ?');
		$query->bind_param( "i", $objet->id);
		$query->execute();
		$query->bind_result( $objet->id, $objet->actif, $objet->titre, $objet->description );
		if(is_null($query->fetch()))
			$objet->id=null;
		$query->close();

		if(!is_null($objet->id)){
			$objet->séries_ids=ThèmeDAO::get_séries_ids($objet->id);
		}
	}

	static function get_séries_ids($id, $inactif=false){
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

	static function get_séries($id, $inactif=false){
		$res=array();
		foreach(ThèmeDAO::get_séries_ids($id,$inactif) as $sérieid){
			$res[]=SérieDAO::get_série($sérieid);
		}
		return $res;
	}
	
	static function get_nb_questions_actives($id){
		$query=ThèmeDAO::$conn->prepare('SELECT count(question.questionID) FROM question, serie WHERE 
question.serieID = serie.serieID AND
question.actif = 1 AND
serie.actif = 1 AND
serie.themeID = ?');
		$query->bind_param( "i", $id);
		$query->execute();
		$query->bind_result($res);
		$query->fetch();
		$query->close();

		return $res;        
	}

}
?>


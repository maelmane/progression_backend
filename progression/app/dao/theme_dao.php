<?php

require_once('entite_dao.php');
require_once(__DIR__.'/../domaine/entités/theme.php');

class ThemeDAO extends EntiteDAO{
    static function get_themes($inactif=false){
		if($inactif){
            $theme_ids=ThemeDAO::$conn->query('SELECT themeID FROM theme WHERE themeID>0 ORDER BY ordre');
		}
		else{
            $theme_ids=ThemeDAO::$conn->query('SELECT themeID FROM theme WHERE 
                                         actif = 1 AND
                                         themeID>0 ORDER BY ordre');
		}
		
		$themes=array();

		$row = $theme_id=$theme_ids->fetch_assoc();
		while( $row ){
			$theme_id=$row['themeID'];
            $themes[] = ThemeDAO::get_theme_par_id($theme_id);;

			$row = $theme_id=$theme_ids->fetch_assoc();
		}
		$theme_ids->close();
		return $themes;
		
	}

	static function get_theme_par_id($id){
		$theme=new Theme($id);

		if(!is_null($id)){
			ThemeDAO::load($theme);
		}

    	return $theme;
	}

	#    protected static function calculer_avancement($themes, $user_id){
	#	foreach($themes as $theme){
	#	    $theme->avancement=$theme->get_pourcentage_avancement($user_id);
	#	}
	#    }
	#    
	protected static function load($objet){
		$query=ThemeDAO::$conn->prepare('SELECT themeID, actif, titre, description FROM theme WHERE themeID = ?');
		$query->bind_param( "i", $objet->id);
		$query->execute();
		$query->bind_result( $objet->id, $objet->actif, $objet->titre, $objet->description );
		if(is_null($query->fetch()))
			$objet->id=null;
		$query->close();

		if(!is_null($objet->id)){
			$objet->series_ids=ThemeDAO::get_series_ids($objet->id);
		}
	}

	static function get_series_ids($id, $inactif=false){
		if($inactif){
			$query=ThemeDAO::$conn->prepare('SELECT serieID FROM serie WHERE
                                             themeID= ? ORDER BY numero');
		}
		else{
			$query=ThemeDAO::$conn->prepare('SELECT serieID FROM serie WHERE
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

	static function get_series($id, $inactif=false){
		$res=array();
		foreach(ThemeDAO::get_series_ids($id,$inactif) as $serieid){
			$res[]=SerieDAO::get_serie_par_id($serieid);
		}
		return $res;
	}
	
	static function get_nb_questions_actives($id){
		$query=ThemeDAO::$conn->prepare('SELECT count(question.questionID) FROM question, serie WHERE 
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


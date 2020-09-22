<?php

require_once('../entite.php');
require_once('../avancement.php');

class AvancementDao extends EntiteDAO {
	public function get_etat(){
		return is_null($this->etat) ? Question::ETAT_DEBUT :  $this->etat;
	}

	public function set_etat($etat){
		if($this->get_etat()==Question::ETAT_DEBUT){
			$query=$GLOBALS["conn"]->prepare('INSERT INTO avancement SET etat = ?, questionID = ?, userID = ?');
			$query->bind_param("sii", $etat, $this->questionID, $this->userID);
			$query->execute();
			$query->close();
			$this->load_info();
		}
		else{
			$query=$GLOBALS["conn"]->prepare('UPDATE avancement SET etat = ? WHERE questionID = ? AND userID = ?');
			$query->bind_param("isi", $etat, $this->questionID, $this->userID);
			$query->execute();
			$query->close();
			$this->etat=$etat;
		}
	}

	public function set_reponse($reponse){
		if($this->get_etat()==Question::ETAT_DEBUT){
			//État par défaut = ETAT_NONREUSSI
			$query=$GLOBALS["conn"]->prepare('INSERT INTO avancement SET etat = 1, reponse = ?, questionID = ?, userID = ?');
			$query->bind_param("sii", $reponse, $this->questionID, $this->userID);
			$query->execute();
			$query->close();
			$this->load_info();
		}
		else{
			$query=$GLOBALS["conn"]->prepare('UPDATE avancement SET reponse = ? WHERE questionID = ? AND userID = ?');
			$query->bind_param("sii", $reponse, $this->questionID, $this->userID);
			$query->execute();
			$query->close();
			$this->reponse=$reponse;
		}
	}
	public function set_conteneur($conteneur){
		if($this->get_etat()==Question::ETAT_DEBUT){
			//État par défaut = ETAT_NONREUSSI
			$query=$GLOBALS["conn"]->prepare('INSERT INTO avancement SET etat = 1, conteneur = ?, questionID = ?, userID = ?');
			$query->bind_param("sii", $conteneur, $this->questionID, $this->userID);
			$query->execute();
			$query->close();
			$this->load_info();
		}
		else{
			$query=$GLOBALS["conn"]->prepare('UPDATE avancement SET conteneur = ? WHERE questionID = ? AND userID = ?');
			$query->bind_param("sii", $conteneur, $this->questionID, $this->userID);
			$query->execute();
			$query->close();
			$this->conteneur=$conteneur;
		}
	}        
?>

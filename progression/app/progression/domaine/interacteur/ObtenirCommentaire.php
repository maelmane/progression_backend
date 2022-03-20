<?php
/*
   This file is part of Progression.

   Progression is free software: you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation, either version 3 of the License, or
   (at your option) any later version.

   Progression is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with Progression.  If not, see <https://www.gnu.org/licenses/>.
 */

namespace progression\domaine\interacteur;

use progression\domaine\entité\Commentaire;
use progression\dao\DAOFactory;

class ObtenirCommentaire extends Interacteur
{
	public function get_commentaire()
	{
    $commentaire = new Commentaire(11, 122456747, "message envoyer poar interacteurMocker", "createur Mock",15);
    $commentaire2 = new Commentaire(13, 1224432747, "message de test", "createur Test",22);
    $commentaires = array($commentaire,$commentaire2);
		return $commentaires;
	}
	public function get_commentaire_par_id($id)
	{
    //$commentaire = new Commentaire(11, 122456747, "message envoyer poar interacteurMocker", "createur Mock",15);
    $commentaire = $this->source_dao->get_commentaire_dao()->get_commentaire($id);
		return $commentaire;
	}
  public function get_commentaire_par_tentative($username, $question_uri, $date)
	{
    //$commentaire = new Commentaire(11, 122456747, "message envoyer poar interacteurMocker", "createur Mock",15);
    $commentaire = $this->source_dao->get_commentaire_dao()->get_toutes($username, $question_uri, $date);
		return $commentaire;
	}

}

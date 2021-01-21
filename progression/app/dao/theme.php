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
?><?php

require_once __DIR__ . '/entite.php';
require_once 'domaine/entités/theme.php';
require_once 'domaine/entités/serie.php';

class ThèmeDAO extends EntiteDAO
{
    static function get_thèmes($inactif = false)
    {
        if ($inactif) {
            $thème_ids = ThèmeDAO::$conn->query(
                'SELECT themeID FROM theme WHERE themeID>0 ORDER BY ordre'
            );
        } else {
            $thème_ids = ThèmeDAO::$conn
                ->query('SELECT themeID FROM theme WHERE 
	                                           actif = 1 AND
	                                           themeID>0 ORDER BY ordre');
        }

        $thèmes = [];

        $row = $thème_id = $thème_ids->fetch_assoc();
        while ($row) {
            $thème_id = $row['themeID'];
            $thèmes[] = ThèmeDAO::get_thème($thème_id);

            $row = $thème_id = $thème_ids->fetch_assoc();
        }
        $thème_ids->close();
        return $thèmes;
    }

    static function get_thème($id)
    {
        $thème = new Thème($id);

        if (!is_null($id)) {
            ThèmeDAO::load($thème);
        }

        return $thème;
    }

    static function get_nb_questions_actives($id)
    {
        $query = ThèmeDAO::$conn
            ->prepare('SELECT count( question.questionID ) FROM question, serie WHERE 
	                                     question.serieID = serie.serieID AND
	                                     question.actif = 1 AND
	                                     serie.actif = 1 AND
	                                     serie.themeID = ?');
        $query->bind_param("i", $id);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();

        return $res;
    }

    protected static function load($objet)
    {
        $query = ThèmeDAO::$conn->prepare(
            'SELECT themeID, actif, titre, description FROM theme WHERE themeID = ?'
        );
        $query->bind_param("i", $objet->id);
        $query->execute();
        $query->bind_result(
            $objet->id,
            $objet->actif,
            $objet->titre,
            $objet->description
        );
        if (is_null($query->fetch())) {
            $objet->id = null;
        }
        $query->close();

        if (!is_null($objet->id)) {
            $objet->séries_ids = SérieDAO::get_séries_ids_par_thème($objet->id);
        }
    }

    function get_avancement($thème_id, $user_id)
    {
        $query = ThèmeDAO::$conn->prepare(
            'SELECT count( question.questionID ) FROM avancement, question, serie WHERE 
	                                     avancement.questionID=question.questionID AND 
	                                     avancement.userID= ? AND 
	                                     question.serieID=serie.serieID AND 
	                                     serie.themeID= ? AND
	                                     question.actif = 1 AND
	                                     serie.actif = 1 AND
	                                     avancement.etat = ' . Question::ETAT_REUSSI
        );
        $query->bind_param("ii", $user_id, $thème_id);
        $query->execute();
        $query->bind_result($res);
        $query->fetch();
        $query->close();

        return $res;
    }
}
?>


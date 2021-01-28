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

namespace progression\domaine\entité;

class Question extends Entité
{
    const ETAT_CACHE = -1;
    const ETAT_DEBUT = 0;
    const ETAT_NONREUSSI = 1;
    const ETAT_REUSSI = 2;

    const TYPE_PROG_EVAL = 0;
    const TYPE_SYS = 1;
    const TYPE_BD = 2;
    const TYPE_PROG = 3;

    public $serieID;
    public $actif;
    public $numero;
    public $titre;
    public $description;
    public $enonce;
    public $feedback_pos;
    public $feedback_neg;
    public $etat;
    public $code_validation;
}

?>

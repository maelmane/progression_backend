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

namespace progression;

use progression\dao\DAOFactory;
use progression\domaine\entité\Question;
use progression\domaine\interacteur\{
    ObtenirSérieInt,
    ObtenirQuestionInt};
use progression\présentation\controleur\{
    AccueilCtl,
    AdminCtl,
    ControleurAdminCtl,
    ControleurSuiviCtl,
    HeaderFooterCtl,
    LoginCtl,
    LogoutCtl,
    PratiqueCtl,
    QuestionBDCtl,
    QuestionProgCtl,
    QuestionProgEvalCtl,
    QuestionSysCtl,
    SérieCtl,
    SuiviCtl,
    ThèmeCtl};

session_start();

require __DIR__ . '/autoload.php';
require __DIR__ . '/../vendor/autoload.php';
require_once 'config.php';

set_locale();
openlog("quiz", LOG_NDELAY, LOG_SYSLOG);
inclusion_page();

function set_locale()
{
    $locale = isset($GLOBALS['config']['locale'])
        ? $GLOBALS['config']['locale']
        : 'fr_CA.UTF-8';
    setlocale(LC_ALL, $locale);
}

function inclusion_page()
{
    $dao_factory = new DAOFactory();

    if (isset($_SESSION["user_id"])) {
        $user_id = $_SESSION['user_id'];

        if (isset($_GET["p"])) {
            $page = $_GET["p"];

            $controleur = null;

            if ($page == "logout") {
                $controleur = new LogoutCtl(null, null);
            }
            if ($page == "theme") {
                $thème_id = $_REQUEST["ID"];
                $controleur = new ThèmeCtl($dao_factory, $user_id, $thème_id);
            } elseif ($page == "serie") {
                $série_id = $_REQUEST["ID"];
                $controleur = new SérieCtl($dao_factory, $série_id, $user_id);
                $thème_id = (new ObtenirSérieInt(
                    $dao_factory,
                    $user_id
                ))->get_série($série_id)->thème_id;
            } elseif ($page == "question") {
                $question_id = $_REQUEST["ID"];
                $question = (new ObtenirQuestionInt(
                    $dao_factory,
                    $user_id
                ))->get_question($question_id);
                $thème_id = (new ObtenirSérieInt(
                    $dao_factory,
                    $user_id
                ))->get_série($question->serieID)->thème_id;

                if ($question->type == Question::TYPE_PROG_EVAL) {
                    $controleur = new QuestionProgEvalCtl(
                        $dao_factory,
                        $user_id,
                        $question->id
                    );
                } elseif ($question->type == Question::TYPE_PROG) {
                    $controleur = new QuestionProgCtl(
                        $dao_factory,
                        $user_id,
                        $question->id
                    );
                } elseif ($question->type == Question::TYPE_SYS) {
                    $controleur = new QuestionSysCtl(
                        $dao_factory,
                        $user_id,
                        $question->id
                    );
                } elseif ($question->type == Question::TYPE_BD) {
                    $controleur = new QuestionBdCtl(
                        $dao_factory,
                        $user_id,
                        $question->id
                    );
                }
            } elseif ($page == "pratique") {
                $controleur = new PratiqueCtl($dao_factory, $user_id);
            } elseif ($page == "ad_suivi") {
                $controleur = new SuiviCtl($dao_factory, $user_id);
            } elseif ($page == "admin") {
                $controleur = new AdminCtl($dao_factory, $user_id);
            }

            if ($controleur == null) {
                $controleur = new AccueilCtl($dao_factory, $user_id);
            }
        } else {
            $controleur = new AccueilCtl($dao_factory, $user_id);
        }
    } else {
        $controleur = new LoginCtl($dao_factory);
    }

    render_page(
        isset($_SESSION['user_id']) ? $user_id : null,
        isset($thème_id) ? $thème_id : null,
        $controleur
    );
}

function render_page($user_id, $thème_id, $controleur)
{
    $infos = [];

    if (!is_null($user_id)) {
        $infos = array_merge(
            (new HeaderFooterCtl(new DAOFactory(), $user_id))->get_header_infos(
                $thème_id,
                $user_id
            )
        );
    }

    syslog(LOG_INFO, "Controleur : " . get_class($controleur));
    $infos = array_merge($infos, $controleur->get_page_infos());

    $template = $GLOBALS['mustache']->loadTemplate($infos["template"]);
    echo $template->render($infos);
}

?>

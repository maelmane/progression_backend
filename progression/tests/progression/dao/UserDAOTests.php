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

namespace progression\dao;

use progression\domaine\entité\Avancement;
use progression\domaine\entité\clé\{Clé, Portée};
use progression\domaine\entité\question\{Question, État};
use progression\domaine\entité\user\{User, Rôle};
use progression\TestCase;

final class UserDAOTests extends TestCase
{
	public $bob = null;

	public function setUp(): void
	{
		parent::setUp();

		$avancement1 = new Avancement([], "Un titre", "facile");
		$avancement1->date_modification = 1615696276;
		$avancement1->date_réussite = null;
		$avancement1->etat = État::NONREUSSI;
		$avancement2 = new Avancement([], "Un titre", "facile");
		$avancement2->date_modification = 1645739981;
		$avancement2->date_réussite = 1645739959;
		$avancement2->etat = État::NONREUSSI;
		$avancement3 = new Avancement([], "Un titre 2", "facile");
		$avancement3->date_modification = 1645739991;
		$avancement3->date_réussite = 1645739969;
		$avancement3->etat = État::REUSSI;

		$this->bob = new User("bob", courriel: "bob@progressionmail.com");
		$this->bob->avancements = [
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction" => $avancement1,
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction" => $avancement2,
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction2" => $avancement3,
		];

		app("db")
			->connection()
			->beginTransaction();
	}

	public function tearDown(): void
	{
		app("db")
			->connection()
			->rollBack();
		parent::tearDown();
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_username_sans_inclusion_on_obtient_son_profil()
	{
		$réponse_attendue = new User("bob", courriel: "bob@progressionmail.com");
		$réponse_attendue->avancements = [];
		$réponse_attendue->clés = [];

		$réponse_observée = (new UserDAO())->get_user("bob");
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_username_EN_MAJUSCULES_sans_inclusion_on_obtient_son_profil()
	{
		$réponse_attendue = new User("bob", courriel: "bob@progressionmail.com");
		$réponse_attendue->avancements = [];
		$réponse_attendue->clés = [];

		$réponse_observée = (new UserDAO())->get_user("BOB");
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_username_incluant_les_avancements_on_obtient_son_profil_et_ses_avancements()
	{
		$réponse_attendue = $this->bob;
		$réponse_attendue->clés = [];

		$réponse_observée = (new UserDAO())->get_user("bob", includes: ["avancements"]);
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_courriel_EN_MAJUSCULES_sans_inclusion_on_obtient_son_profil()
	{
		$réponse_attendue = new User("bob", courriel: "bob@progressionmail.com");
		$réponse_attendue->avancements = [];
		$réponse_attendue->clés = [];

		$réponse_observée = (new UserDAO())->trouver(courriel: "BOB@progressionmail.com");
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_username_incluant_les_clés_on_obtient_son_profil_et_ses_clés()
	{
		$réponse_attendue = $this->bob;
		$réponse_attendue->avancements = [];
		$réponse_attendue->clés = [
			"clé de test" => new Clé(null, 1624593600, 1624680000, Portée::AUTH),
			"clé de test 2" => new Clé(null, 1624593602, 1624680002, Portée::AUTH),
		];

		$réponse_observée = (new UserDAO())->get_user("bob", includes: ["clés"]);
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_username_incluant_les_avancements_et_les_clés_on_obtient_son_profil_et_ses_avancements_et_clés()
	{
		$réponse_attendue = $this->bob;
		$réponse_attendue->clés = [
			"clé de test" => new Clé(null, 1624593600, 1624680000, Portée::AUTH),
			"clé de test 2" => new Clé(null, 1624593602, 1624680002, Portée::AUTH),
		];

		$réponse_observée = (new UserDAO())->get_user("bob", includes: ["avancements", "clés"]);
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_courriel_sans_inclusion_on_obtient_son_profil()
	{
		$réponse_attendue = new User("bob", courriel: "bob@progressionmail.com");
		$réponse_attendue->avancements = [];
		$réponse_attendue->clés = [];

		$réponse_observée = (new UserDAO())->trouver(courriel: "bob@progressionmail.com");
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_username_et_courriel_sans_inclusion_on_obtient_son_profil()
	{
		$réponse_attendue = new User("bob", courriel: "bob@progressionmail.com");
		$réponse_attendue->avancements = [];
		$réponse_attendue->clés = [];

		$réponse_observée = (new UserDAO())->trouver(username: "bob", courriel: "bob@progressionmail.com");
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_inexistant_lorsquon_le_cherche_par_son_courriel_on_obtient_null()
	{
		$réponse_observée = (new UserDAO())->trouver(courriel: "inconnu@nullepart.com");
		$this->assertNull($réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_inexistant_lorsquon_le_cherche_par_son_username_on_obtient_null()
	{
		$réponse_observée = (new UserDAO())->get_user("alice");
		$this->assertNull($réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_le_cherche_par_son_username_avec_le_courriel_dun_autre_utilisateur_on_obtient_null()
	{
		$réponse_attendue = null;

		$réponse_observée = (new UserDAO())->trouver("bob", courriel: "jane@gmail.com");
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_le_cherche_par_son_username_avec_un_courriel_inexistant_on_obtient_null()
	{
		$réponse_attendue = null;

		$réponse_observée = (new UserDAO())->trouver("bob", courriel: "inconnum@nullepart.com");
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_inexistant_lorsquon_le_sauvegarde_il_est_créé_dans_la_BD_et_on_obtient_son_profil()
	{
		$réponse_attendue = new User("gaston");
		$user_test = new User("gaston");

		$réponse_observée = (new UserDAO())->save($user_test);
		$this->assertEquals($réponse_attendue, $réponse_observée);

		$réponse_observée = (new UserDAO())->get_user("gaston");
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_le_sauvegarde_il_est_modifié_dans_la_BD_et_on_obtient_son_profil_modifié()
	{
		$avancement1 = new Avancement([], "Un titre", "facile");
		$avancement2 = new Avancement([], "Un titre 2", "facile");

		$réponse_attendue = new User("bob", courriel: "bob@progressionmail.com", rôle: Rôle::ADMIN);
		$réponse_attendue->avancements = [];
		$réponse_attendue->clés = [];

		$user_test = (new UserDAO())->get_user("bob");
		$user_test->rôle = Rôle::ADMIN;

		$réponse_observée = (new UserDAO())->save($user_test);
		$this->assertEquals($réponse_attendue, $réponse_observée);

		$réponse_observée = (new UserDAO())->get_user("bob");
		$this->assertEquals($réponse_attendue, $réponse_observée);
	}

	public function test_étant_donné_un_utilisateur_lorsquon_vérifie_un_mot_de_passe_correct_on_obtient_vrai()
	{
		$user = new User("bob");

		$dao = new UserDAO();
		$dao->set_password($user, "test de mot de passe");

		$this->assertTrue($dao->vérifier_password($user, "test de mot de passe"));
	}

	public function test_étant_donné_un_utilisateur_lorsquon_vérifie_un_mot_de_passe_incorrect_on_obtient_faux()
	{
		$user = new User("bob");

		$dao = new UserDAO();
		$dao->set_password($user, "test de mot de passe");

		$this->assertFalse($dao->vérifier_password($user, "Mauvais mot de passe"));
	}

	public function test_étant_donné_un_utilisateur_lorsquon_change_son_mot_de_passe_et_vérifie_l_ancien_on_obtient_faux()
	{
		$user = new User("bob");

		$dao = new UserDAO();
		$dao->set_password($user, "test de mot de passe");
		$dao->set_password($user, "Nouveau mot de passe");

		$this->assertTrue($dao->vérifier_password($user, "Nouveau mot de passe"));

		$this->assertFalse($dao->vérifier_password($user, "test de mot de passe"));
	}
}

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

use progression\domaine\entité\{Avancement, User, Clé};
use progression\TestCase;
use DB;
final class UserDAOTests extends TestCase
{
	public function setUp(): void
	{
		parent::setUp();
		EntitéDAO::get_connexion()->begin_transaction();
	}

	public function tearDown(): void
	{
		parent::tearDown();
		EntitéDAO::get_connexion()->rollback();
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_username_on_obtient_son_profil()
	{
		$avancement1 = new Avancement(1, 3, [], [], "Bob", "facile", 1645739981, 1645739959);
		$avancement2 = new Avancement(0, 3, [], [], "Bob", "facile", 1645739981, 1645739959);

		$réponse_attendue = new User("bob");
		$réponse_attendue->avancements = [];
		$réponse_attendue->clés = [];

		$résponse_observée = (new UserDAO())->get_user("bob");
		$this->assertEquals($réponse_attendue, $résponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_username_incluant_les_avancements_on_obtient_son_profil_et_ses_avancements()
	{
		$avancement1 = new Avancement(1, 3, [], [], "Bob", "facile", 1645739981, 1645739959);
		$avancement2 = new Avancement(0, 3, [], [], "Bob", "facile", 1645739981, 1645739959);

		$réponse_attendue = new User("bob");
		$réponse_attendue->avancements = [
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction" => $avancement1,
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction" => $avancement2,
		];
		$réponse_attendue->clés = [];

		$résponse_observée = (new UserDAO())->get_user("bob", ["avancements"]);
		$this->assertEquals($réponse_attendue, $résponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_username_incluant_les_clés_on_obtient_son_profil_et_ses_clés()
	{
		$avancement1 = new Avancement(1, 3, [], [], "Bob", "facile", 1645739981, 1645739959);
		$avancement2 = new Avancement(0, 3, [], [], "Bob", "facile", 1645739981, 1645739959);

		$réponse_attendue = new User("bob");
		$réponse_attendue->avancements = [];
		$réponse_attendue->clés = [
			"clé de test" => new Clé(null, 1624593600, 1624680000, Clé::PORTEE_AUTH),
			"clé de test 2" => new Clé(null, 1624593602, 1624680002, Clé::PORTEE_AUTH),
		];

		$résponse_observée = (new UserDAO())->get_user("bob", ["clés"]);
		$this->assertEquals($réponse_attendue, $résponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_cherche_par_son_username_incluant_les_avancements_et_les_clés_on_obtient_son_profil_et_ses_avancements_et_clés()
	{
		DB::enableQueryLog();
		$avancement1 = new Avancement(1, 3, [], [], "Bob", "facile", 1645739981, 1645739959);
		$avancement2 = new Avancement(0, 3, [], [], "Bob", "facile", 1645739981, 1645739959);

		$réponse_attendue = new User("bob");
		$réponse_attendue->avancements = [
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_autre_fonction" => $avancement1,
			"https://depot.com/roger/questions_prog/fonctions01/appeler_une_fonction" => $avancement2,
		];
		$réponse_attendue->clés = [
			"clé de test" => new Clé(null, 1624593600, 1624680000, Clé::PORTEE_AUTH),
			"clé de test 2" => new Clé(null, 1624593602, 1624680002, Clé::PORTEE_AUTH),
		];

		$résponse_observée = (new UserDAO())->get_user("bob", ["avancements", "clés"]);
		print_r(DB::getQueryLog());
		$this->assertEquals($réponse_attendue, $résponse_observée);
	}

	public function test_étant_donné_un_utilisateur_inexistant_lorsquon_le_cherche_par_son_username_on_obtient_null()
	{
		$réponse_attendue = null;

		$résponse_observée = (new UserDAO())->get_user("alice");
		$this->assertEquals($réponse_attendue, $résponse_observée);
	}

	public function test_étant_donné_un_utilisateur_inexistant_lorsquon_le_sauvegarde_il_est_créé_dans_la_BD_et_on_obtient_son_profil()
	{
		$réponse_attendue = new User("gaston");
		$user_test = new User("gaston");

		$résponse_observée = (new UserDAO())->save($user_test);
		$this->assertEquals($réponse_attendue, $résponse_observée);

		$résponse_observée = (new UserDAO())->get_user("gaston");
		$this->assertEquals($réponse_attendue, $résponse_observée);
	}

	public function test_étant_donné_un_utilisateur_existant_lorsquon_le_sauvegarde_il_est_modifié_dans_la_BD_et_on_obtient_son_profil_modifié()
	{
		$avancement1 = new Avancement(1, 3, [], [], "Bob", "facile", 1645739981, 1645739959);
		$avancement2 = new Avancement(0, 3, [], [], "Bob", "facile", 1645739981, 1645739959);

		$réponse_attendue = new User("bob", User::ROLE_ADMIN);
		$réponse_attendue->avancements = [];
		$réponse_attendue->clés = [];

		$user_test = (new UserDAO())->get_user("bob");
		$user_test->rôle = User::ROLE_ADMIN;

		$résponse_observée = (new UserDAO())->save($user_test);
		$this->assertEquals($réponse_attendue, $résponse_observée);

		$résponse_observée = (new UserDAO())->get_user("bob");
		$this->assertEquals($réponse_attendue, $résponse_observée);
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

	public function test_étant_donné_un_utilisateur_lorsquon_vérifie_un_mot_de_passe_null_on_obtient_faux()
	{
		$user = new User("bob");

		$dao = new UserDAO();
		$dao->set_password($user, "test de mot de passe");

		$this->assertFalse($dao->vérifier_password($user, null));
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

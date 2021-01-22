<?php
use PHPUnit\Framework\TestCase;

require __DIR__ . '/../../../app/domaine/entités/avancement.php';

final class AvancementTest extends TestCase{
    public function test_étant_donné_un_avancement_instancié_avec_questionid_5_et_userid_3_lorsquon_récupère_son_questionid_on_obtient_5(){
        $avancementTest = new Avancement(5, 3);

        $questionid = $avancementTest->question_id;

        $this->assertEquals( 5, $questionid );
    }

    public function test_étant_donné_un_avancement_instancié_avec_questionid_5_et_userid_3_lorsquon_récupère_son_userid_on_obtient_3(){
        $avancementTest = new Avancement(5, 3);

        $userid = $avancementTest->user_id;

        $this->assertEquals( 3, $userid );
    }

}

?>

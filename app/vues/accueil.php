<?php

require_once('quiz_preambule.php');

page_header("Tableau de bord");
page_content();
page_footer();

function page_content(){
    $themes=get_themes();
    calculer_avancement($themes,$_SESSION['user_id']);
    render_page($themes);
}

function calculer_avancement($themes, $user_id){
    foreach($themes as $theme){
        $theme->avancement=$theme->get_pourcentage_avancement($user_id);
    }
}

function render_page($themes){
    $template=$GLOBALS['mustache']->loadTemplate("accueil");
    echo $template->render(array('themes'=>$themes));
}

?>

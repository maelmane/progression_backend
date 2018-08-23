
<?php
require_once('quiz_preambule.php');

$theme=charger_theme_ou_terminer();

page_header($theme->titre);
page_content($theme);

function charger_theme_ou_terminer(){
    $theme=new Theme($_GET['ID'], $_SESSION['user_id']);

    if(is_null($theme->id)){
        header('Location: index.php?p=accueil');
    }

    return $theme;
}

function page_content($theme){
    $series=$theme->get_series();
    calculer_avancement($series, $_SESSION['user_id']);
    render_page($theme, $series);
}

function calculer_avancement($series, $user_id){
    foreach($series as $serie){
        $serie->avancement=$serie->get_pourcentage_avancement($user_id);
    }
}

function render_page($theme, $series){
    $template=$GLOBALS['mustache']->loadTemplate("theme");
    echo $template->render(array('theme'=>$theme, 'series'=>$series));
}

?>

<?php

page_header();

if($user->role!=1){
    header('Location: index.php?p=accueil');
}

$user=charger_user_ou_terminer();
rôle_admin_ou_terminer($user);
render_page();

function charger_user_ou_terminer(){
    $user=new User();
    $user->id=$_SESSION['user_id'];
    if(is_null($user->id)){
        header('Location: index.php?p=accueil');
    }
    $user->load_info();

    return $user;
}

function rôle_admin_ou_terminer($user){
    if($user->role!=User::ROLE_ADMIN){
        header('Location: index.php?p=accueil');
    }
}

function render_page(){
    echo '
        <ul>
        <li><a href="index.php?p=ad_ajout_q">Ajouter des questions</a></li>
        <li><a href="index.php?p=ad_suivi">Suivi</a></li>
        ';
}

?>

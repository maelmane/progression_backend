<?php

page_header();

$user=charger_user_ou_terminer();
rôle_admin_ou_terminer( $user );
page_contenu();

function charger_user_ou_terminer(){
    $user=new User( $_SESSION[ 'user_id' ] );
    if( is_null( $user->id )){
        header( 'Location: index.php?p=accueil' );
    }

    return $user;
}

function rôle_admin_ou_terminer( $user ){
    if( $user->role!=User::ROLE_ADMIN ){
        header( 'Location: index.php?p=accueil' );
    }
}

?>

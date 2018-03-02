<?php

$themes = get_themes($_SESSION['user_id']);

?>

<html>
  <head>
    <meta charset='utf-8'>
    <link rel='stylesheet' type='text/css' href='css/style.css'>
    <title><?php echo $titre . " - Progression"; ?></title>
    <!-- Ajouté UIKit et JQuery Voir doc UIKit : https://getuikit.com/docs/introduction -->
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="css/uikit.min.css" />
    <script src="js/uikit.min.js"></script>
    <script src="js/uikit-icons.min.js"></script>
    <script src='https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js'></script>
    <link href="https://fonts.googleapis.com/css?family=Lato" rel="stylesheet">
    <link rel='stylesheet' type='text/css' href='css/newstyle.css'>
  </head>
  <body>
    <div uk-grid style="background-color:#FFE9DF;">

      <!-- Logo -->
      <div class="uk-width-1-3" id="main-logo">
        <a href='index.php'>
          <h1>Prog<span>ression</span></h1>
        </a>
      </div>

      <!-- Page main subject / exercise -->
      <div class="uk-width-1-3">
        <h3 class="subtitle"><?php echo $titre; ?></h3>
      </div>

      <!-- Menu bar -->
      <div class="uk-width-1-3">

        <!-- Bouton Menu Offcanvas -->
        <a class="menu-button" ahref="#offcanvas-slide" uk-toggle="target: #offcanvas-slide; animation: uk-animation-fade; queued: true" uk-toggle></a>

        <!-- Nom utilisateur -->
        <div class="name-user">
          <h2><?php echo ($_SESSION["username"]=="adminquiz") ? "<a href='?p=admin'>Admin</a>" : $_SESSION["nom"]; ?></h2>
        </div>

        <!-- OffCanvas Menu -->
        <div id="offcanvas-slide" style="position:fixed" uk-offcanvas="overlay: true;flip: true;mode: slide">
          <div class="uk-offcanvas-bar">
            
            <button class="uk-offcanvas-close uk-close-large" type="button" uk-close></button>

            <ul class="uk-nav uk-nav-default">

              <li <?php if($titre == "Tableau de bord") echo 'class="uk-active"' ?>><a href='?p=dashboard'>Tableau de bord</a></li>
              <!-- Liste de tout les thèmes -->
              <?php foreach($themes as $theme) { echo "<li " . ($theme->titre == $titre ? 'class=uk-active' : '') . "><a href='?p=theme&ID=$theme->id'>$theme->titre</a></li>";} ?>
            </ul>
          </div>
        </div>
      </div>
    </div>
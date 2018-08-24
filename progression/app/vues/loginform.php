<html>
  <head>
    <meta charset="utf-8">
    <title>Progression</title>
    <link rel="stylesheet" type="text/css" href="css/style.css">
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <!-- jQuery library -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <!-- Latest compiled JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
  </head>
  <body id="login">
    <?php if(isset($erreur)) { echo "<div class='alert alert-danger'> $erreur </div>"; } ?>
    <section class="main">
      <div class="example-wrapper clearfix">
        <div style="align:center"><img src="images/logo.png"></div>
        <div class="container" id="centre">
          <form name="login" method="POST" class="form-horizontal">
            <div class="form-group">
              <label id="loginTxt" class="control-label col-sm-3">Courriel : </label>
              <div class="col-sm-3">
                <input class="form-control" type="text" name="username" />
              </div>
              <div class="col-sm-3">
                        <label style="text-align:left;color:#888;"><?php if(isset($GLOBALS['config']['domaine_mail'])) echo $GLOBALS['config']['domaine_mail'] ?></label>
              </div>
            </div>
            <?php if($GLOBALS['config']['auth_type']!="no"){ echo '
            <div class="form-group">
              <label id="loginTxt"  class="control-label col-sm-3">Mot de passe : </label>
              <div class="col-sm-3">
                <input class="form-control" name="passwd" type="password"/>
              </div>
            </div>
            ';} ?>
            <div class="col-sm-offset-3 uk-margin">
              <input name="submit" type="submit" class="btn btn-primary" value="Connexion">
            </div>
          </form>
        </div>
      </div>
    </section>
  </body>
</html>

<?php
session_start();
if(isset($_POST['connexion'])){
  if(empty($_POST['pseudo'])){
    echo "Le champ Pseudo est vide.";
  } else {
    if(empty($_POST['mdp'])){
      echo "Le champ Mot de passe est vide.";
    } else {
      $Pseudo = htmlentities($_POST['pseudo'], ENT_QUOTES, "UTF-8"); 
      $MotDePasse = htmlentities($_POST['mdp'], ENT_QUOTES, "UTF-8");
      $mysqli = mysqli_connect("localhost", "root", "root", "efrei_rdv", 3307);
      if(!$mysqli){
        echo "Erreur de connexion à la base de données.";
      } else {
        $Requete = mysqli_query($mysqli,"SELECT * FROM membres WHERE pseudo = '".$Pseudo."' AND mdp = '".$MotDePasse."'");
        if(mysqli_num_rows($Requete) == 0) {
          echo "Le pseudo ou le mot de passe est incorrect, le compte n'a pas été trouvé.";
        } else {
            $_SESSION['pseudo'] = $Pseudo;
            header("Location: Accueil.php");
            exit();
        }
      }
    }
  }
}
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Département informatique de l'efrei</title>
    <link rel="stylesheet" href="../css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
    <header id="header_connexion">
      <h1>
          <img src="../img/logo-efrei.png" id="logo" width="430" height="120">
          DEPARTEMENT INFORMATIQUE DE L'EFREI
      </h1>
    </header>
    <main>
      <form action="connexion.php" method="post">
        <fieldset id="connexion">
          <legend>Connexion</legend>
          <div id="pseudo_grp">
            <label id="pseudo" for="pseudo">Pseudo : </label>
            <input type="text" name="pseudo" />
          </div>
          <div id="mdp_grp">
            <label id ="mdp" for="mdp">Mot de passe : </label>
            <input type="password" name="mdp" />
          </div>
          <input type="submit" name="connexion" value="Connexion" />
        </fieldset>
      </form>
    </main>
    <?php include ("footer.php"); ?>
</body>
</html>
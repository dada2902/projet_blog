<?php 
include 'inc/init.inc.php'; // connexion à la BDD + des outils
include 'inc/fonctions.inc.php'; // les fonctions utilisateur

// Deconnexion utilisateur
//------------------------
if( isset($_GET['action']) && $_GET['action'] == 'deconnexion') {
    session_destroy(); // on détruit la session pour la déconnexion
}
//------------------------
//------------------------


// Si l'utilisateur est connecté, on le redirige vers profil.php (restriction d'accès)
if( user_is_connected() ) {
  header('location:profil.php');
  exit();
}

$pseudo = '';


// L'utilisateur demande une connexion
if(isset($_POST['pseudo']) && isset($_POST['mdp'])) {
  $pseudo = trim($_POST['pseudo']);
  $mdp = trim($_POST['mdp']);

  $connexion = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
  $connexion->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
  $connexion->execute();

  // on vérifie avec rowCount() si on a une ligne, 1 ligne récupérer = pseudo ok en bdd
  if($connexion->rowCount() > 0) {

    // le pseudo est bon, on applique un tech pour transformer la ligne en tableau array pour pouvoir vérifier le mdp 
    $membre = $connexion->fetch(PDO::FETCH_ASSOC);

    // on vérifie le mdp
    if( password_verify($mdp, $membre['mdp'])) {

      // on place les informations de l'utilisateur dans la $_SESSION
      $_SESSION['membre'] = array();
      $_SESSION['membre']['id_membre'] = $membre['id_membre'];
      $_SESSION['membre']['pseudo'] = $membre['pseudo'];
      $_SESSION['membre']['nom'] = $membre['nom'];
      $_SESSION['membre']['prenom'] = $membre['prenom'];
      $_SESSION['membre']['id_avatar'] = $membre['id_avatar'];
      $_SESSION['membre']['email'] = $membre['email'];
      $_SESSION['membre']['sexe'] = $membre['sexe'];
      $_SESSION['membre']['statut'] = $membre['statut'];

      // Maintenant que la connexion est ok, on redirige vers la page profil
      header('location:profil.php');

    } else {
      $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le pseudo et/ou mot de passe. <br> Veuillez vérifier vos saisies.</div>';   
    }

  } else {
    $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le pseudo et/ou mot de passe. <br> Veuillez vérifier vos saisies.</div>';   
  }
}





// Début des affichages !
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
// echo '<pre>'; var_dump($_SESSION); echo '</pre>';
?>


        <main>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Connexion <i class="fas fa-sign-in-alt text-info"></i></h1><hr>
                        <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-6 mx-auto mt-5">
                        <form method="post" action="">
                            <div class="form-group">
                                <label for="pseudo">Pseudo</label>
                                <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?php echo $pseudo; ?>">
                            </div>
                            <div class="form-group">
                                <label for="mdp">Mot de passe</label>
                                <input type="text" name="mdp" id="mdp" class="form-control" value="">
                            </div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" id="inscription" class="btn btn-info w-100">Connexion <i class="fas fa-sign-in-alt"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>


<?php
include 'inc/footer.inc.php';

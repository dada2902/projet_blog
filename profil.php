<?php 

// 03 - Gestion Avatar // ok
// Enregistrer des images d'avatar dans le projet //ok
// affichage dans un tableau html // ok
// Possibilité de supprimer // ok
// Depuis le profil mettre un lien d'une nouvelle page listant les avatars disponibles
// Permettre à un utilisateur de choisir un avatar et lui affecter dans la BDD puis l'afficher sur sa page profil 

?>
<?php
include 'inc/init.inc.php'; // connexion à la BDD + des outils
include 'inc/fonctions.inc.php'; // les fonctions utilisateur

// restriction d'accès
if( !user_is_connected() ) { // si l'utilisateur n'est PAS connecté
  header('location:connexion.php'); // on redirige vers connexion
  exit(); // on bloque l'exécution du code à la suite
}

// $_SESSION['membre']['id_avatar'] = $membre['id_avatar'];

$recup_avatar = $pdo->prepare("SELECT avatar_src FROM avatar WHERE id_avatar = :id_avatar");
$recup_avatar->bindParam(':id_avatar',$_SESSION['membre']['id_avatar'],  PDO::PARAM_STR);
$recup_avatar->execute();

if($recup_avatar->rowCount() > 0) {

    // le pseudo est bon, on applique un tech pour transformer la ligne en tableau array pour pouvoir vérifier le mdp 
    $avatar = $recup_avatar->fetch(PDO::FETCH_ASSOC);
    $avatar_src = $avatar['avatar_src'];
}
// ... CODE PHP ...


// Début des affichages !
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
// echo '<pre>'; var_dump($_SESSION); echo '</pre>';
?>


      <main>
        <div class="container">
          <div class="row">
                <div class="col-12">
                    <h1>Profil</h1><hr>
                    <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                </div>
          </div>
          <div class="row mt-5">
                <div class="col-sm-6">
                    <!-- <img class="img-thumbnail" src="img/profil_<?php echo $statut; ?>.jpg" alt=""> -->
                    <!-- <img class="img-thumbnail" src="img/profil_<?php echo $statut; ?>.jpg"> -->

                        <?php 

                        if(empty($avatar_src)){
                            if($_SESSION['membre']['statut'] == 1) {
                                echo '<img class="img-thumbnail" src="img/profil_membre.jpg">';
                            } elseif ($_SESSION['membre']['statut'] == 2) {
                                echo '<img class="img-thumbnail" src="img/profil_admin.jpg">';
                            } else { 
                                echo '<img class="img-thumbnail" src="img/profil_integrateur.jpg">';
                            }

                        } else {
                            echo '<img class="img-thumbnail" src="' . $avatar_src . '" >';

                        }
                           


                            
                            
                        ?>
                </div>
                <div class="col-sm-6">
                    <ul class="list-group">
                        <li class="list-group-item active" style="background-color: #e75480;">Vos informations</li>
                        <li class="list-group-item"><b>Pseudo: </b> <?php echo $_SESSION['membre']['pseudo'] ?></li>
                        <li class="list-group-item">Numéro membre: <?php echo $_SESSION['membre']['id_membre'] ?></li>
                        <li class="list-group-item">Nom: <?php echo $_SESSION['membre']['nom'] ?></li>
                        <li class="list-group-item">Prénom: <?php echo $_SESSION['membre']['prenom'] ?></li>
                        <li class="list-group-item">Email: <?php echo $_SESSION['membre']['email'] ?></li>

                        <?php if($_SESSION['membre']['sexe'] == 'f') { $sexe = 'femme';} else { $sexe = 'homme';} ?>

                        <li class="list-group-item">Sexe: <?php echo $sexe ?></li>

                        <?php if($_SESSION['membre']['statut'] == 1 ) { $statut = 'membre';} elseif   ($_SESSION['membre']['statut'] == 2 ) { $statut = 'administrateur';} else { $statut = 'Intégrateur';} ?>

                        <li class="list-group-item">Statut: <?php echo $statut ?></li>
                        <li class="list-group-item"><a href="http://localhost/php/projet_blog/avatar.php/">Les avatars disponibles</a><?php  ?></li>

                    </ul>
                </div>
          </div>
        </div>
      </main>


<?php 
include 'inc/footer.inc.php';


      
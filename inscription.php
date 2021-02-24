<?php 
include 'inc/init.inc.php'; // connexion à la BDD + des outils
include 'inc/fonctions.inc.php'; // les fonctions utilisateur

// Si l'utilisateur est connecté, on le redirige vers profil.php
if( user_is_connected() ) {
    header('location:profil.php');
    exit();
}

$pseudo = '';
$email = '';
$nom = '';
$prenom = '';
$sexe ='';
// Enregistrement de l'inscription
if( isset($_POST['pseudo']) && isset($_POST['mdp']) && isset($_POST['confirm_mdp']) && isset($_POST['email']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['sexe']) ) {
    // on récupère les informations dans des variables plus simple d'écriture et au passage on applique un trim() pour enlebver les espaces en début et en fin de chaine.
    $pseudo = trim($_POST['pseudo']);
    $mdp = trim($_POST['mdp']);
    $confirm_mdp = trim($_POST['confirm_mdp']);
    $email = trim($_POST['email']);
    $nom = trim($_POST['nom']);
    $prenom = trim($_POST['prenom']);
    $sexe = trim($_POST['sexe']);

    // Controles sur les données du formulaires
    //-----------------------------------------

    // variable de controle pour savoir s'il y a eu des erreurs lors de nos controles
    $erreur = false;

    // 01 . validation des caractères du pseudo : on autorise les lettes , les chiffes le . le . le _ (pas les caractères spéciaux)
    $verif_caractere = preg_match( '#^[a-zA-Z0-9._-]+$#', $pseudo );
    /*
        EXPRESSION REGULIERE (regex)
        ---------------------------
        les # représentent le début et la fin de la regex
        le ^ indique que le début de la chaine ne peut pas commencer par autre  hcose que les caractères proposés dans les []
        le $ indique que la fin de la chaine ne peut pas finir par autre chose que les caractères proposés dans les  []
        le + indique que lo'on peut aveoir plusieurs fois le même caractère
        dans les [] on a les caractères autorisées
    */

    // if($verif_caractere == 0)
    if(!$verif_caractere) {
        // erreur sur les caractères du pseudo : message d'erreur pour l'utilisateur dans $msg + changement de la valeur de la viable $erreur
        // $msg = $msg . ''; // c'est la même chose
        $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le pseudo, ⚠<br>Caractères autorisés : les lettres a-z, les chiffres 0-9 et les ".", le "-" et le "_".<br>Veuillez vérifier vos saisies.</div>';
        $erreur = true;
    }

    // 02 . controle sur la taille du pseudo
    if( iconv_strlen($pseudo) < 4 || iconv_strlen($pseudo) > 14) {
        $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le pseudo, ⚠<br>Le pseudo doit avoir entre 4 et 14 caractères inclus<br>Veuillez vérifier vos saisies.</div>';
        // en cas d'erreur
        $erreur = true;
    }

    // 03 . controle sur la disponibilité du pseudo (car unique en BDD)
    // pour savoir si le pseudo est disponible, on déclaenche une requete en BDD de récupération sur la base du pseudo.
    // Si on obtient 1 ligne, le pseudo est indisponible
    // Si on obtient 0 ligne, le pseudo est disponible

    $verif_pseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $verif_pseudo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $verif_pseudo->execute();

    // on vérifie l'erreur donc s'il y a au moins une ligne dans la réponse
    if($verif_pseudo->rowCount() > 0) {
        $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le pseudo,<br>Le pseudo est indisponible. Veuillez vérifier vos saisies.</div>';       
        $erreur = true; 
    }

    // .............................................

    // 04 . controle sur la disponibilité du email (car unique en BDD)
    // pour savoir si le pseudo est disponible, on déclaenche une requete en BDD de récupération sur la base du pseudo.
    // Si on obtient 1 ligne, le pseudo est indisponible
    // Si on obtient 0 ligne, le pseudo est disponible

    $verif_email = $pdo->prepare("SELECT * FROM membre WHERE email = :email");
    $verif_email->bindParam(':email', $email, PDO::PARAM_STR);
    $verif_email->execute();

    // on vérifie l'erreur donc s'il y a au moins une ligne dans la réponse
    if($verif_email->rowCount() > 0) {
        $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le email, <br>L\'email est indisponible. Veuillez vérifier vos saisies.</div>';       
        $erreur = true; 
    }

    // 05 . controle sur la validite du format de l'email
    if(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le mail,<br>Le format du mail est invalide. Veuillez vérifier vos saisies.</div>';       
        $erreur = true;
    }

    // 06 . controle entre le mdp et le confirm_mdp
    if($mdp != $confirm_mdp) {
        $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le mot de passe,<br>Les saisaies du mot de passe et la confirmation ne correspondent pas. Veuillez vérifier vos saisies.</div>';       
        $erreur = true;
    }

    // fin des controles, nous allons pouvoir insérer si tout est ok (si $erreur est == à false alros il n'y a pas eu de cas d'erreur dans nos controles)
    if(!$erreur) {
        // ok tout va bien peut lancer l'insert into

        // Cryptage (hashage) du mdp
        $mdp = password_hash($mdp, PASSWORD_DEFAULT);

        $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, email, nom, prenom, sexe, statut) VALUES (:pseudo, :mdp, :email, :nom, :prenom, :sexe, 1)");
        // valeur du statut:
        // 1 => membre
        // 2 => administrateur
        $enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $enregistrement->bindParam(':mdp', $mdp, PDO::PARAM_STR);
        $enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
        $enregistrement->bindParam(':nom', $nom, PDO::PARAM_STR);
        $enregistrement->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $enregistrement->bindParam(':sexe', $sexe, PDO::PARAM_STR);
        $enregistrement->execute();

        // mail($email, 'Confirmation d\'inscription', 'Bonjour, merci de vous être inscrit sur notre site. Blablabla', 'From: monsite@mail.fr');

        // Maintenant que l'inscription est ok, on redirige vers la page de connexion
        header('location:connexion.php');
    }

} // FIN DES IF ISSET validation du formulaire

// Créer 2 comptes en BDD (un compte admin, un compte utilisateur standard)
// admin admin
// test test






// Début des affichages !
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
// echo '<pre>'; var_dump($_POST); echo '</pre>';
?>


        <main>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Inscription <i class="fas fa-ghost text-primary"></i></h1>
                        <?php echo $msg; // on affiche si c'est vide  ?>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-12 mt-5">
                        <form method="post" action="" class="p-3 border">

                            <div class="row">
                                <div class="col-sm-6">
                                    <div class="form-group">
                                        <label for="pseudo">Pseudo</label>
                                        <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?php echo $pseudo; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="mdp">Mot de passe</label>
                                        <input type="text" name="mdp" id="mdp" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="confirm_mdp">Confirmation mot de passe</label>
                                        <input type="text" name="confirm_mdp" id="confirm_mdp" class="form-control" value="">
                                    </div>
                                    <div class="form-group">
                                        <label for="email">Email</label>
                                        <input type="text" name="email" id="email" class="form-control" value="<?php echo $email; ?>">
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                <div class="form-group">
                                        <label for="nom">Nom</label>
                                        <input type="text" name="nom" id="nom" class="form-control" value="<?php echo $nom; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="prenom">Prénom</label>
                                        <input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo $prenom; ?>">
                                    </div>
                                    <div class="form-group">
                                        <label for="sexe">Sexe</label>
                                        <select name="sexe" id="sexe" class="form-control">
                                            <option value="m">Homme</option>
                                            <option value="f" <?php if($sexe == 'f') { echo "selected"; } ?> >Femme</option>
                                        </select>
                                    </div>
                                    <div class="form-group">
                                        <label>&nbsp;</label>
                                        <button type="submit" id="inscription" class="btn btn-primary w-100" style="box-shadow: 2px 2px 2px 2px rgba(0, 0, 255, .2);">Inscription <i class="fas fa-sign-in-alt"></i></button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>


<?php
include 'inc/footer.inc.php';

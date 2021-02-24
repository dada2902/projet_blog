<?php


// 02 - Gestion Membre
// Formulaire d'ajout Membre
// affichage dans un tableau html
// Possibilité de modifier // OK      &          supprimer // OK
// Possibilité de changer le statut d'un membre (admin / membre) // OK
// Ajouter un nouveau statut (intégrateur - 3), ce statut permet d'accéder uniquement à la gestion article. Attention aux liens de menu // OK


// SELECT pseudo, email, nom, prenom, sexe, statut FROM membre ORDER BY (id_membre)

?>
<?php 
include '../inc/init.inc.php'; // connexion à la BDD + des outils
include '../inc/fonctions.inc.php'; // les fonctions utilisateur

if( !user_is_admin() ) {
    header("location:../connexion.php");
    exit();
}

// SUPPRESSION d'un membre
if( isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_membre']) )  {
    $suppression = $pdo->prepare("DELETE FROM membre WHERE id_membre = :id_membre");
    $suppression->bindParam(":id_membre", $_GET['id_membre'], PDO::PARAM_STR);
    $suppression->execute();
}

// MODIFICATION d'un membre : on récupère les infos en BDD avant la modif

$pseudo = '';
$email = '';
$mdp = '';
// $confirm_mdp = '';
$nom = '';
$prenom = '';
$sexe ='';
$statut ='';
$id_membre ='';


if( isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_membre']) )  {

    $recup_membre = $pdo->prepare("SELECT id_membre, pseudo, email, mdp, nom, prenom, sexe, statut FROM membre WHERE id_membre = :id_membre");
    $recup_membre->bindParam(":id_membre", $_GET['id_membre'], PDO::PARAM_STR);
    $recup_membre->execute();

    if($recup_membre->rowCount() > 0) {

        $infos_membre = $recup_membre->fetch(PDO::FETCH_ASSOC);

        $id_membre = $infos_membre['id_membre'];
        $pseudo = $infos_membre['pseudo'];
        $email = $infos_membre['email'];
        $mdp = $infos_membre['mdp'];
        $nom =  $infos_membre['nom'];
        $prenom =  $infos_membre['prenom'];
        $sexe =  $infos_membre['sexe'];
        $statut =  $infos_membre['statut'];
    }

}

//------------------------------------------------------------------------------

// Ajout et modification d'un membre

if( isset($_POST['pseudo']) && isset($_POST['email']) && isset($_POST['mdp']) && isset($_POST['confirm_mdp']) && isset($_POST['nom']) && isset($_POST['prenom']) && isset($_POST['sexe']) && isset($_POST['id_membre']) ) {

    // echo '<pre>'; var_dump($_POST); echo '</pre>';

    $pseudo = trim($_POST['pseudo']);
    $email = trim($_POST['email']);
    $nom = trim($_POST['nom']);
    $mdp = trim($_POST['mdp']);
    $confirm_mdp = trim($_POST['confirm_mdp']);
    $prenom = trim($_POST['prenom']);
    $sexe = trim($_POST['sexe']);
    $statut = trim($_POST['statut']);
    $id_membre = trim($_POST['id_membre']);

    $erreur = false;

    $verif_caractere = preg_match( '#^[a-zA-Z0-9._-]+$#', $pseudo );

    if(!$verif_caractere) {
        $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le pseudo, ⚠<br>Caractères autorisés : les lettres a-z, les chiffres 0-9 et les ".", le "-" et le "_".<br>Veuillez vérifier vos saisies.</div>';
        $erreur = true;
    }

    // 02 . controle sur la taille du pseudo
    if( iconv_strlen($pseudo) < 4 || iconv_strlen($pseudo) > 14) {
        $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le pseudo, ⚠<br>Le pseudo doit avoir entre 4 et 14 caractères inclus<br>Veuillez vérifier vos saisies.</div>';
        // en cas d'erreur
        $erreur = true;
    }

    $verif_pseudo = $pdo->prepare("SELECT * FROM membre WHERE pseudo = :pseudo");
    $verif_pseudo->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
    $verif_pseudo->execute();

    // on vérifie l'erreur donc s'il y a au moins une ligne dans la réponse
    if($verif_pseudo->rowCount() > 0 && empty($id_membre)) {
        $msg .= '<div class="alert alert-danger mb-3">⚠ Erreur sur le pseudo,<br>Le pseudo est indisponible. Veuillez vérifier vos saisies.</div>';       
        $erreur = true; 
    }

    $verif_email = $pdo->prepare("SELECT * FROM membre WHERE email = :email");
    $verif_email->bindParam(':email', $email, PDO::PARAM_STR);
    $verif_email->execute();

    // on vérifie l'erreur donc s'il y a au moins une ligne dans la réponse
    if($verif_email->rowCount() > 0 && empty($id_membre)) {
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

    // SI IL N'Y A PLUS D'ERREUR 

    if(!$erreur && empty($id_membre)) { 

        
        $enregistrement = $pdo->prepare("INSERT INTO membre (pseudo, mdp, email, nom, prenom, sexe, statut) VALUES (:pseudo, :mdp, :email, :nom, :prenom, :sexe, 1)");

        $mdp = password_hash($mdp, PASSWORD_DEFAULT);
    

        $enregistrement->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $enregistrement->bindParam(':email', $email, PDO::PARAM_STR);
        $enregistrement->bindParam(':mdp', $mdp, PDO::PARAM_STR);
        $enregistrement->bindParam(':nom', $nom, PDO::PARAM_STR);
        $enregistrement->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $enregistrement->bindParam(':sexe', $sexe, PDO::PARAM_STR);
        $enregistrement->execute(); 

    } else {

        $modification = $pdo->prepare("UPDATE membre SET pseudo = :pseudo, email = :email, mdp = :mdp, nom = :nom, prenom = :prenom, sexe = :sexe, statut = :statut WHERE id_membre = :id_membre");

        $modification->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
        $modification->bindParam(':pseudo', $pseudo, PDO::PARAM_STR);
        $modification->bindParam(':email', $email, PDO::PARAM_STR);
        $modification->bindParam(':mdp', $mdp, PDO::PARAM_STR);
        $modification->bindParam(':nom', $nom, PDO::PARAM_STR);
        $modification->bindParam(':prenom', $prenom, PDO::PARAM_STR);
        $modification->bindParam(':sexe', $sexe, PDO::PARAM_STR);
        $modification->bindParam(':statut', $statut, PDO::PARAM_STR);
        $modification->execute(); 

    }

         

        
}// FIN DE ISSET

$liste_membre1 = $pdo->query("SELECT id_membre, pseudo, email, mdp, nom, prenom, sexe, statut FROM membre ORDER BY (id_membre)");
// Début des affichages !
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
// echo '<pre>'; var_dump($_POST); echo '</pre>';
?>


        <main>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h3>Ajout de nouveaux membres <i class="fas fa-ghost text-info"></i></h3><hr>
                        <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-12">
                        <form method="post" action="" >
                            <!-- ajout d'un champ caché pour conserver l'id_article lors d'une modification -->
                            <input type="hidden" name="id_membre" value="<?php echo $id_membre   ?>"> 

                            <div class="form-group">
                                <label for="pseudo">Pseudo</label>
                                <input type="text" name="pseudo" id="pseudo" class="form-control" value="<?php echo $pseudo   ?>">
                            </div>

                            <div class="form-group">
                                <label for="email">Email</label>
                                <input type="email" name="email" id="email" class="form-control" value="<?php echo $email   ?>">
                            </div>

                            <div class="form-group">
                                <label for="mdp">Mot de passe</label>
                                <input type="text" name="mdp" id="mdp" class="form-control" value="<?php echo $mdp   ?>">
                            </div>

                            <div class="form-group">
                                <label for="confirm_mdp">Confirmation mot de passe</label>
                                <input type="text" name="confirm_mdp" id="confirm_mdp" class="form-control" value="<?php echo $mdp   ?>">
                            </div>                              

                            <div class="form-group">
                                <label for="nom">Nom</label>
                                <input type="text" name="nom" id="nom" class="form-control" value="<?php echo $nom   ?>">
                            </div>

                            <div class="form-group">
                                <label for="prenom">Prénom</label>
                                <input type="text" name="prenom" id="prenom" class="form-control" value="<?php echo $prenom   ?>">
                            </div>   
                            
                            <div class="form-group">
                                <label for="sexe">Sexe</label>
                                <select name="sexe" id="sexe" class="form-control">
                                    <option value="m">Homme</option>
                                    <option value="f" <?php if($sexe == 'f') { echo "selected"; } ?> >Femme</option>
                                </select>
                            </div>  

                            <div class="form-group">
                                <label for="statut">Statut</label>
                                <select name="statut" id="statut" class="form-control">
                                    <option value="1">Membre</option>
                                    <option value="2" <?php if($statut == '2') { echo "selected"; } ?> >Administrateur</option>
                                    <option value="3" <?php if($statut == '3') { echo "selected"; } ?> >Integrateur</option>
                                </select>
                            </div>  

                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" id="enregistrement" class="btn btn-info w-100" style="box-shadow: 2px 2px 2px 2px rgba(0, 0, 255, .2);">Ajouter / Modifier / Changement de statut <i class="fas fa-sign-in-alt"></i></button>
                            </div>                             
                                                

                        </form>
                    </div>
                </div>

                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered mt-5">
                            <tr>
                                <th>N° membre</th>
                                <th>Pseudo</th>
                                <th>Email</th> 
                                <th>Mot de passe</th>
                                <th>Nom</th>
                                <th>Prenom</th>
                                <th>Sexe</th>
                                <th>Statut</th>
                                <th>Action</th>
                            </tr>
                            <?php 

                                while($membre1 = $liste_membre1->fetch(PDO::FETCH_ASSOC)) {
                                    // var_dump($membre);

                                    echo '<tr>';                              

                                    echo '<td>' . $membre1['id_membre'] . '</td>';
                                    echo '<td>' . $membre1['pseudo'] . '</td>';
                                    echo '<td>' . $membre1['email'] . '</td>';
                                    echo '<td>' . substr($membre1['mdp'], 0, 15) . '...</td>';
                                    echo '<td>' . $membre1['nom'] . '</td>';
                                    echo '<td>' . $membre1['prenom'] . '</td>';
                                    echo '<td>' . $membre1['sexe'] . '</td>';
                                    echo '<td>' . $membre1['statut'] . '</td>';

                                    // boutons pour les actions :
                                    echo '<td>';
                                    // bouton modifier
                                    echo '<a href="?action=modifier&id_membre=' . $membre1['id_membre'] . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a> ';
                                    // bouton supprimer
                                    echo '<a href="?action=supprimer&id_membre=' . $membre1['id_membre'] . '" onclick="return(confirm(\'Etes vous sûr ?\'))" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>';
                                    echo '</td>';

                                    echo '</tr>';

                                }
                            
                            ?>

                        </table>
                    </div>
                </div>
            </div>++-

        </main>


<?php
include '../inc/footer.inc.php';


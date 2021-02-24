<?php
include 'inc/init.inc.php'; // connexion à la BDD + des outils
include 'inc/fonctions.inc.php'; // les fonctions utilisateur

$img = '';
$id_avatar = '';

if( isset($_GET['action']) && $_GET['action'] == 'selectionner' && !empty($_GET['id_avatar']) )  {

    $recup_avatar = $pdo->prepare("SELECT id_avatar, avatar_src FROM avatar WHERE id_avatar = :id_avatar");
    $recup_avatar->bindParam(":id_avatar", $_GET['id_avatar'], PDO::PARAM_STR);
    $recup_avatar->execute();

    if($recup_avatar->rowCount() > 0) {
        $infos_avatar = $recup_avatar->fetch(PDO::FETCH_ASSOC);

        $id_avatar = $infos_avatar['id_avatar'];
        $img =  $infos_avatar['avatar_src'];
    }

    $enregistrement_avatar = $pdo->prepare("UPDATE membre SET id_avatar = :id_avatar WHERE id_membre = :id_membre");
    $enregistrement_avatar->bindParam(":id_avatar", $_GET['id_avatar'], PDO::PARAM_STR);
    $enregistrement_avatar->bindParam(":id_membre", $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
    $enregistrement_avatar->execute();

    $_SESSION['membre']['id_avatar'] = $_GET['id_avatar'];


}


// ... CODE PHP ...
$liste_avatar = $pdo->query("SELECT id_avatar, avatar_src FROM avatar ORDER BY id_avatar ");
$liste_avatar2 = $pdo->query("SELECT id_avatar, avatar_src FROM avatar ORDER BY id_avatar ");



// Début des affichages !
include 'inc/header.inc.php';
// include 'inc/nav.inc.php';
?>


      <main>
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>Liste des avatars <i class="fas fa-ghost text-info"></i></h1><hr>
                    <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                </div>              

            </div>
            <div class="row">
                <div class="col-12">
                    <h6><a href="http://localhost/php/projet_blog/profil.php/">Retour</a></h6><hr>
                    <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                </div>                         
            </div>
            <div class="row">
                <div class="col-12">
                    <table class="table table-bordered mt-5">
                        <tr>
                            <th>N° avatar</th>
                            <th>Image avatar</th>                                
                            <th>Actions</th>
                        </tr>
                        <?php 

                            while($avatar2 = $liste_avatar2->fetch(PDO::FETCH_ASSOC)) {
                                // var_dump($avatar_src);

                                echo '<tr>';                              

                                echo '<td>' . $avatar2['id_avatar'] . '</td>';
                                echo '<td><img src="' . $avatar2['avatar_src'] . '" style="width: 100px;" class="img-thumbnail"></td>';
                                
                                // boutons pour les actions :
                                echo '<td>';
                                // bouton modifier
                                echo '<a href="?action=selectionner&id_avatar=' . $avatar2['id_avatar'] . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a> ';
                                // bouton supprimer
                                // echo '<a href="?action=supprimer&id_avatar=' . $avatar['id_avatar'] . '" onclick="return(confirm(\'Etes vous sûr ?\'))" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>';
                                // echo '</td>';

                                // echo '</tr>';

                            }
                        
                        ?>
                    </table>
                </div>

            </div>
        </div>
      </main>


<?php 
include 'inc/footer.inc.php';


      
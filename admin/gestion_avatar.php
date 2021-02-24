<?php

// 03 - Gestion Avatar
// Enregistrer des images d'avatar_src dans le projet
// affichage dans un tableau html
// Possibilité de supprimer
// Depuis le profil mettre un lien d'une nouvelle page listant les avatars disponibles
// Permettre à un utilisateur de choisir un avatar_src et lui affecter dans la BDD puis l'afficher sur sa page profil

?>
<?php 
include '../inc/init.inc.php'; // connexion à la BDD + des outils
include '../inc/fonctions.inc.php'; // les fonctions utilisateur

if( !user_is_admin() ) {
    header("location:../connexion.php");
    exit();
}

// SUPPRESSION d'une catégorie
if( isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_avatar']) )  {
    $suppression = $pdo->prepare("DELETE FROM avatar WHERE id_avatar = :id_avatar");
    $suppression->bindParam(":id_avatar", $_GET['id_avatar'], PDO::PARAM_STR);
    $suppression->execute();
}


// MODIFICATION d'une catégorie : on récupère les infos en BDD avant la modif

$img = '';
$id_avatar = '';

if( isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_avatar']) )  {

    $recup_avatar = $pdo->prepare("SELECT id_avatar, avatar_src FROM avatar WHERE id_avatar = :id_avatar");
    $recup_avatar->bindParam(":id_avatar", $_GET['id_avatar'], PDO::PARAM_STR);
    $recup_avatar->execute();

    if($recup_avatar->rowCount() > 0) {
        $infos_avatar = $recup_avatar->fetch(PDO::FETCH_ASSOC);

        $id_avatar = $infos_avatar['id_avatar'];
        $img =  $infos_avatar['avatar_src'];
    }

}

//------------------------------------------------------------------------------

// Enregistrement et modification d'une catégorie

if(isset($_POST['avatar_src']) && isset($_POST['id_avatar'])) {
    // echo '<pre>'; var_dump($_POST); echo '</pre>';

    $img = trim($_POST['avatar_src']);
    $id_avatar = trim($_POST['id_avatar']);
     
    if( empty($id_avatar) ) { // si $id_avatar est vide, c'est un enregistrement sinon, c'est une modification.

        $nouvelle_avatar = $pdo->prepare("INSERT INTO avatar (id_avatar, avatar_src) VALUES (:id_avatar, :avatar_src )");
        $nouvelle_avatar->bindParam(':id_avatar', $_SESSION['avatar']['id_avatar'], PDO::PARAM_STR);
        $nouvelle_avatar->bindParam(':avatar_src', $img, PDO::PARAM_STR);
        $nouvelle_avatar->execute();

    } else { // modification

        $modification = $pdo->prepare("UPDATE avatar SET avatar_src = :avatar_src WHERE id_avatar = :id_avatar");
        $modification->bindParam(':id_avatar', $id_avatar, PDO::PARAM_STR);
        $modification->bindParam(':avatar_src', $img, PDO::PARAM_STR);
        $modification->execute();
        
    }
}

$liste_avatar = $pdo->query("SELECT id_avatar, avatar_src FROM avatar ORDER BY id_avatar ");






// Début des affichages !
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
// echo '<pre>'; var_dump($_POST); echo '</pre>';
?>


        <main>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Gestion des avatars <i class="fas fa-ghost text-info"></i></h1><hr>
                        <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-12">
                        <form method="post" action="">
                            <!-- ajout d'un champ caché pour conserver l'id_article lors d'une modification -->
                            <input type="hidden" name="id_avatar" value="<?php echo $id_avatar   ?>">

                            <div class="form-group">
                                <label for="avatar_src">Image avatar</label>
                                <input type="text" name="avatar_src" id="avatar_src" class="form-control" value="<?php echo $img  ?>">
                            </div>

                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" id="enregistrement" class="btn btn-info w-100" style=" box-shadow: 2px 2px 2px 2px rgba(0, 0, 255, .2);">Enregistrement <i class="fas fa-sign-in-alt"></i></button>
                            </div>

                        </form>
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
                            <tr>
                                <th colspan="7">
                                    <input type="text" id="search" class="form-control w-100" placeholder="Rechercher">
                                </th>
                            </tr>

                            <?php 

                                while($avatar = $liste_avatar->fetch(PDO::FETCH_ASSOC)) {
                                    // var_dump($avatar_src);

                                    echo '<tr>';                              

                                    echo '<td>' . $avatar['id_avatar'] . '</td>';
                                    echo '<td><img src="' . $avatar['avatar_src'] . '" style="width: 100px;" class="img-thumbnail"></td>';
                                    
                                    // boutons pour les actions :
                                    echo '<td>';
                                    // bouton modifier
                                    echo '<a href="?action=modifier&id_avatar=' . $avatar['id_avatar'] . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a> ';
                                    // bouton supprimer
                                    echo '<a href="?action=supprimer&id_avatar=' . $avatar['id_avatar'] . '" onclick="return(confirm(\'Etes vous sûr ?\'))" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>';
                                    echo '</td>';

                                    echo '</tr>';

                                }
                            
                            ?>

                        </table>
                    </div>
                </div>
            </div>
        </main>


<?php
include '../inc/footer.inc.php';


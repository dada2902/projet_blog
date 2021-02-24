<?php

// 04 - Gestion Mot clés
// Formulaire d'ajout Mot clés
// affichage dans un tableau html
// Possibilité de modifier & supprimer // ok

// SELECT * FROM `mot_cle` ORDER BY (id_mot_cle)

?>
<?php 
include '../inc/init.inc.php'; // connexion à la BDD + des outils
include '../inc/fonctions.inc.php'; // les fonctions utilisateur

if( !user_is_admin() ) {
    header("location:../connexion.php");
    exit();
}

// SUPPRESSION d'un mot clé
if( isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_mot_cle']) )  {
    $suppression = $pdo->prepare("DELETE FROM mot_cle WHERE id_mot_cle = :id_mot_cle");
    $suppression->bindParam(":id_mot_cle", $_GET['id_mot_cle'], PDO::PARAM_STR);
    $suppression->execute();
}


// MODIFICATION d'un mot clé : on récupère les infos en BDD avant la modif

$nom = '';
$description = '';
$id_mot_cle = '';

if( isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_mot_cle']) )  {

    $recup_mot_cle = $pdo->prepare("SELECT id_mot_cle, titre_mot_cle, description_mot_cle FROM mot_cle WHERE id_mot_cle = :id_mot_cle");
    $recup_mot_cle->bindParam(":id_mot_cle", $_GET['id_mot_cle'], PDO::PARAM_STR);
    $recup_mot_cle->execute();

    if($recup_mot_cle->rowCount() > 0) {
        $infos_mot_cle = $recup_mot_cle->fetch(PDO::FETCH_ASSOC);

        $id_mot_cle = $infos_mot_cle['id_mot_cle'];
        $nom =  $infos_mot_cle['titre_mot_cle'];
        $description =  $infos_mot_cle['description_mot_cle'];
    }

}

//------------------------------------------------------------------------------

// Enregistrement et modification d'une catégorie

if(isset($_POST['titre_mot_cle']) && isset($_POST['description_mot_cle']) && isset($_POST['id_mot_cle'])) {
    // echo '<pre>'; var_dump($_POST); echo '</pre>';

    $nom = trim($_POST['titre_mot_cle']);
    $description = trim($_POST['description_mot_cle']);
    $id_mot_cle = trim($_POST['id_mot_cle']);
     
    if( empty($id_mot_cle) ) { // si $id_mot_cle est vide, c'est un enregistrement sinon, c'est une modification.

        $nouvelle_mot_cle = $pdo->prepare("INSERT INTO mot_cle (id_mot_cle, titre_mot_cle, description_mot_cle) VALUES (:id_mot_cle, :titre_mot_cle, :description_mot_cle)");
        $nouvelle_mot_cle->bindParam(':id_mot_cle', $_SESSION['mot_cle']['id_mot_cle'], PDO::PARAM_STR);
        $nouvelle_mot_cle->bindParam(':titre_mot_cle', $nom, PDO::PARAM_STR);
        $nouvelle_mot_cle->bindParam(':description_mot_cle', $description, PDO::PARAM_STR);
        $nouvelle_mot_cle->execute();

    } else { // modification

        $modification = $pdo->prepare("UPDATE mot_cle SET titre_mot_cle = :titre_mot_cle, description_mot_cle = :description_mot_cle WHERE id_mot_cle = :id_mot_cle");
        $modification->bindParam(':id_mot_cle', $id_mot_cle, PDO::PARAM_STR);
        $modification->bindParam(':titre_mot_cle', $nom, PDO::PARAM_STR);
        $modification->bindParam(':description_mot_cle', $description, PDO::PARAM_STR);
        $modification->execute();
        
    }
}

$liste_mot_cle = $pdo->query("SELECT id_mot_cle, titre_mot_cle, description_mot_cle FROM mot_cle ORDER BY id_mot_cle ");






// Début des affichages !
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
// echo '<pre>'; var_dump($_POST); echo '</pre>';
?>


        <main>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Gestion des mots clés <i class="fas fa-ghost text-info"></i></h1><hr>
                        <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-12">
                        <form method="post" action="">
                            <!-- ajout d'un champ caché pour conserver l'id_article lors d'une modification -->
                            <input type="hidden" name="id_mot_cle" value="<?php echo $id_mot_cle   ?>">

                            <div class="form-group">
                                <label for="titre_mot_cle">Mot clé</label>
                                <input type="text" name="titre_mot_cle" id="titre_mot_cle" class="form-control" value="<?php echo $nom  ?>">
                            </div>
            
                            <div class="form-group">
                                <label for="description_mot_cle">Description</label>
                                <textarea name="description_mot_cle" rows="7" id="titre_description" class="form-control"><?php echo $description  ?></textarea>
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
                                <th>N° </th>
                                <th>Mot clé</th>                                
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                            <tr>
                                <th colspan="7">
                                    <input type="text" id="search" class="form-control w-100" placeholder="Rechercher">
                                </th>
                            </tr>

                            <?php 

                                while($mot_cle = $liste_mot_cle->fetch(PDO::FETCH_ASSOC)) {
                                    // var_dump($mot_cle);

                                    echo '<tr>';                              

                                    echo '<td>' . $mot_cle['id_mot_cle'] . '</td>';
                                    echo '<td>' . $mot_cle['titre_mot_cle'] . '</td>';
                                    echo '<td>' . substr($mot_cle['description_mot_cle'], 0, 40) . '...</td>';
                                    
                                    // boutons pour les actions :
                                    echo '<td>';
                                    // bouton modifier
                                    echo '<a href="?action=modifier&id_mot_cle=' . $mot_cle['id_mot_cle'] . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a> ';
                                    // bouton supprimer
                                    echo '<a href="?action=supprimer&id_mot_cle=' . $mot_cle['id_mot_cle'] . '" onclick="return(confirm(\'Etes vous sûr ?\'))" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>';
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


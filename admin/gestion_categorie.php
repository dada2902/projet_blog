<?php

// Faire la page gestion_categorie.php
// Dans cette page faire un formulaire permettant de créer une catégorie en BDD
// Mettre en place l'enregistrement de la catégorie en BDD
// Ensuite :
// Faire un tableau sous le formulaire pour lister les catégories
// Mettre en place deux outils permettant de modifier ou de supprimer une catégorie

?>
<?php 
include '../inc/init.inc.php'; // connexion à la BDD + des outils
include '../inc/fonctions.inc.php'; // les fonctions utilisateur

if( !user_is_admin() ) {
    header("location:../connexion.php");
    exit();
}

// SUPPRESSION d'une catégorie
if( isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_categorie']) )  {
    $suppression = $pdo->prepare("DELETE FROM categorie WHERE id_categorie = :id_categorie");
    $suppression->bindParam(":id_categorie", $_GET['id_categorie'], PDO::PARAM_STR);
    $suppression->execute();
}


// MODIFICATION d'une catégorie : on récupère les infos en BDD avant la modif

$nom = '';
$description = '';
$id_categorie = '';

if( isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_categorie']) )  {

    $recup_categorie = $pdo->prepare("SELECT id_categorie, titre_categorie, description_categorie FROM categorie WHERE id_categorie = :id_categorie");
    $recup_categorie->bindParam(":id_categorie", $_GET['id_categorie'], PDO::PARAM_STR);
    $recup_categorie->execute();

    if($recup_categorie->rowCount() > 0) {
        $infos_categorie = $recup_categorie->fetch(PDO::FETCH_ASSOC);

        $id_categorie = $infos_categorie['id_categorie'];
        $nom =  $infos_categorie['titre_categorie'];
        $description =  $infos_categorie['description_categorie'];
    }

}

//------------------------------------------------------------------------------

// Enregistrement et modification d'une catégorie

if(isset($_POST['titre_categorie']) && isset($_POST['description_categorie']) && isset($_POST['id_categorie'])) {
    // echo '<pre>'; var_dump($_POST); echo '</pre>';

    $nom = trim($_POST['titre_categorie']);
    $description = trim($_POST['description_categorie']);
    $id_categorie = trim($_POST['id_categorie']);
     
    if( empty($id_categorie) ) { // si $id_categorie est vide, c'est un enregistrement sinon, c'est une modification.

        $nouvelle_categorie = $pdo->prepare("INSERT INTO categorie (id_categorie, titre_categorie, description_categorie) VALUES (:id_categorie, :titre_categorie, :description_categorie)");
        $nouvelle_categorie->bindParam(':id_categorie', $_SESSION['categorie']['id_categorie'], PDO::PARAM_STR);
        $nouvelle_categorie->bindParam(':titre_categorie', $nom, PDO::PARAM_STR);
        $nouvelle_categorie->bindParam(':description_categorie', $description, PDO::PARAM_STR);
        $nouvelle_categorie->execute();

    } else { // modification

        $modification = $pdo->prepare("UPDATE categorie SET titre_categorie = :titre_categorie, description_categorie = :description_categorie WHERE id_categorie = :id_categorie");
        $modification->bindParam(':id_categorie', $id_categorie, PDO::PARAM_STR);
        $modification->bindParam(':titre_categorie', $nom, PDO::PARAM_STR);
        $modification->bindParam(':description_categorie', $description, PDO::PARAM_STR);
        $modification->execute();
        
    }
}

$liste_categorie = $pdo->query("SELECT id_categorie, titre_categorie, description_categorie FROM categorie ORDER BY id_categorie ");






// Début des affichages !
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
// echo '<pre>'; var_dump($_POST); echo '</pre>';
?>


        <main>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Gestion des catégories <i class="fas fa-ghost text-info"></i></h1><hr>
                        <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-12">
                        <form method="post" action="">
                            <!-- ajout d'un champ caché pour conserver l'id_article lors d'une modification -->
                            <input type="hidden" name="id_categorie" value="<?php echo $id_categorie   ?>">

                            <div class="form-group">
                                <label for="titre_categorie">Catégories</label>
                                <input type="text" name="titre_categorie" id="titre_categorie" class="form-control" value="<?php echo $nom  ?>">
                            </div>
            
                            <div class="form-group">
                                <label for="description_categorie">Description</label>
                                <textarea name="description_categorie" rows="7" id="titre_description" class="form-control"><?php echo $description  ?></textarea>
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
                                <th>N° categorie</th>
                                <th>Nom</th>                                
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                            <tr>
                                <th colspan="7">
                                    <input type="text" id="search" class="form-control w-100" placeholder="Rechercher">
                                </th>
                            </tr>

                            <?php 

                                while($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC)) {
                                    // var_dump($categorie);

                                    echo '<tr>';                              

                                    echo '<td>' . $categorie['id_categorie'] . '</td>';
                                    echo '<td>' . $categorie['titre_categorie'] . '</td>';
                                    echo '<td>' . substr($categorie['description_categorie'], 0, 40) . '...</td>';
                                    
                                    // boutons pour les actions :
                                    echo '<td>';
                                    // bouton modifier
                                    echo '<a href="?action=modifier&id_categorie=' . $categorie['id_categorie'] . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a> ';
                                    // bouton supprimer
                                    echo '<a href="?action=supprimer&id_categorie=' . $categorie['id_categorie'] . '" onclick="return(confirm(\'Etes vous sûr ?\'))" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>';
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


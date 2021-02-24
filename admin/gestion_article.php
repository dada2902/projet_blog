<?php 
include '../inc/init.inc.php'; // connexion à la BDD + des outils
include '../inc/fonctions.inc.php'; // les fonctions utilisateur

//-----------------------------------------------------------
//-----------------------------------------------------------
// restriction d'accès
// si l'utilisateur n'est pas admin, on le redirige vers connexion.php
//-----------------------------------------------------------
//-----------------------------------------------------------


if( !user_is_admin() && !user_is_integrateur() ) {
    header("location:../connexion.php");
    exit();
}



//-----------------------------------------------------------
//-----------------------------------------------------------
// SUPPRESSION de l'article
//-----------------------------------------------------------
//-----------------------------------------------------------
if( isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_article']) )  {
    $suppression = $pdo->prepare("DELETE FROM article WHERE id_article = :id_article");
    $suppression->bindParam(":id_article", $_GET['id_article'], PDO::PARAM_STR);
    $suppression->execute();
}

//-----------------------------------------------------------
//-----------------------------------------------------------
// MODIFICATION de l'article : on récupère les infos en BDD avant la modif
//-----------------------------------------------------------
//-----------------------------------------------------------
$id_article = '';
$titre = '';
$contenu = '';
$image_principale = '';
$id_categorie = '';

if( isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_article']) )  {
    $recup_article = $pdo->prepare("SELECT id_article, titre, contenu, image_principale, id_categorie FROM article INNER JOIN relation_article_categorie USING (id_article) WHERE id_article = :id_article");
    $recup_article->bindParam(":id_article", $_GET['id_article'], PDO::PARAM_STR);
    $recup_article->execute();

    if($recup_article->rowCount() > 0) {
        $infos_article = $recup_article->fetch(PDO::FETCH_ASSOC);

        $id_article = $infos_article['id_article'];
        $titre =  $infos_article['titre'];
        $contenu =  $infos_article['contenu'];
        $image_principale =  $infos_article['image_principale'];
        $id_categorie =  $infos_article['id_categorie'];
    }

}


//-----------------------------------------------------------
//-----------------------------------------------------------
// récupération de la liste des catégories en BDD pour les afficher dans le select option du formulaire
//-----------------------------------------------------------
//-----------------------------------------------------------
$liste_categorie = $pdo->query("SELECT * FROM categorie ORDER BY titre_categorie");


//-----------------------------------------------------------
//-----------------------------------------------------------
// Enregistrement de l'article
//-----------------------------------------------------------
//-----------------------------------------------------------
if( isset($_POST['titre']) && isset($_POST['image_principale']) && isset($_POST['categorie']) && isset($_POST['contenu']) && isset($_POST['id_article']) ) {

    $titre = trim($_POST['titre']);
    $image_principale = trim($_POST['image_principale']);

    $categorie = trim($_POST['categorie']);
    $id_categorie = trim($_POST['categorie']);
    
    $contenu = trim($_POST['contenu']);
    // Pour la modif 
    $id_article = trim($_POST['id_article']);

    if( empty($id_article) ) { // si $id_article est vide, c'est un enregistrement sinon, c'est une modification.
        // enregistrement
        $enregistrement = $pdo->prepare("INSERT INTO article (id_membre, titre, date_enregistrement, contenu, image_principale, etat) VALUES (:id_membre, :titre, now(), :contenu, :image_principale, 'public')");
        $enregistrement->bindParam(':id_membre', $_SESSION['membre']['id_membre'], PDO::PARAM_STR);
        $enregistrement->bindParam(':titre', $titre, PDO::PARAM_STR);
        $enregistrement->bindParam(':contenu', $contenu, PDO::PARAM_STR);
        $enregistrement->bindParam(':image_principale', $image_principale, PDO::PARAM_STR);
        $enregistrement->execute();

        // Pour lier la catégorie à l'article que nous venons de créer, on récupère le dernier id inséré dans la BDD (celui de l'article que l'on vient de créer)
        $id_article = $pdo->lastInsertId();

        $enregistrement_relation = $pdo->prepare("INSERT INTO relation_article_categorie (id_article, id_categorie) VALUES (:id_article, :id_categorie)");
        $enregistrement_relation->bindParam(':id_article', $id_article, PDO::PARAM_STR);
        $enregistrement_relation->bindParam(':id_categorie', $categorie, PDO::PARAM_STR);
        $enregistrement_relation->execute();

    } else {
        // modification
        $modification = $pdo->prepare("UPDATE article SET titre = :titre, contenu = :contenu, image_principale = :image_principale WHERE id_article = :id_article");
        $modification->bindParam(':id_article', $id_article, PDO::PARAM_STR);
        $modification->bindParam(':titre', $titre, PDO::PARAM_STR);
        $modification->bindParam(':contenu', $contenu, PDO::PARAM_STR);
        $modification->bindParam(':image_principale', $image_principale, PDO::PARAM_STR);
        $modification->execute();

        $modification_relation = $pdo->prepare("UPDATE relation_article_categorie SET id_categorie = :id_categorie WHERE id_article = :id_article");
        $modification_relation->bindParam(':id_categorie', $categorie, PDO::PARAM_STR);
        $modification_relation->bindParam(':id_article', $id_article, PDO::PARAM_STR);
        $modification_relation->execute();

    }

    

}

//-----------------------------------------------------------
//-----------------------------------------------------------
// récupération des articles avec leur catégorie pour l'affichage dans le tableau html
//-----------------------------------------------------------
//-----------------------------------------------------------
$liste_article = $pdo->query("SELECT id_article, titre, date_enregistrement, contenu, image_principale, titre_categorie FROM article INNER JOIN relation_article_categorie USING (id_article) INNER JOIN categorie USING (id_categorie)");




// Début des affichages !
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
// echo '<pre>'; var_dump($_POST); echo '</pre>';
?>


        <main>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Gestion des articles <i class="fas fa-ghost text-info"></i></h1><hr>
                        <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                    </div>                    
                </div>
                <div class="row">
                    <div class="col-12 mt-5">
                        <form method="post" action="">
                            <!-- ajout d'un champ caché pour conserver l'id_article lors d'une modification -->
                            <input type="hidden" name="id_article" value="<?php echo $id_article; ?>">

                            <div class="form-group">
                                <label for="titre">Titre</label>
                                <input type="text" name="titre" id="titre" class="form-control" value="<?php echo $titre; ?>">
                            </div>
                            <div class="form-group">
                                <label for="image_principale">Image principale</label>
                                <input type="text" name="image_principale" id="image_principale" class="form-control" value="<?php echo $image_principale; ?>">
                            </div>
                            <div class="form-group">
                                <label for="categorie">Catégorie</label>
                                <select name="categorie" id="categorie" class="form-control">

                                <?php 

                                while($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC)) {
                                    
                                    if($categorie['id_categorie'] == $id_categorie) {
                                        echo '<option value="' . $categorie['id_categorie'] . '" selected >' . $categorie['titre_categorie'] . '</option>';
                                    } else {
                                        echo '<option value="' . $categorie['id_categorie'] . '">' . $categorie['titre_categorie'] . '</option>';
                                    }
                                    /*
                                    $selected = '';
                                    if($categorie['id_categorie'] == $id_categorie) {
                                        $selected = 'selected';
                                    }
                                    echo '<option value="' . $categorie['id_categorie'] . '" ' . $selected . ' >' . $categorie['titre_categorie'] . '</option>';
                                    */
                                }
                                
                                ?>

                                </select>
                            </div>
                            <div class="form-group">
                                <label for="contenu">Contenu</label>
                                <textarea name="contenu" rows="7" id="contenu" class="form-control"><?php echo $contenu; ?></textarea>
                            </div>
                            <div class="form-group">
                                <label>&nbsp;</label>
                                <button type="submit" id="enregistrement" class="btn btn-info w-100" style="box-shadow: 2px 2px 2px 2px rgba(0, 0, 255, .2);">Enregistrement <i class="fas fa-sign-in-alt"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="row">
                    <div class="col-12">
                        <table class="table table-bordered mt-5">
                            <tr>
                                <th>N° article</th>
                                <th>Titre</th>
                                <th>Date</th>
                                <th>Contenu</th>
                                <th>Image</th>
                                <th>Catégorie</th>
                                <th>Actions</th>
                            </tr>
                            <tr>
                                <th colspan="7">
                                    <input type="text" id="search" class="form-control w-100" placeholder="Rechercher">
                                </th>
                            </tr>

                            <?php 

                                while($article = $liste_article->fetch(PDO::FETCH_ASSOC)) {
                                    // var_dump($article);

                                    echo '<tr>';

                                    /*
                                    foreach($article AS $valeur) {
                                        echo '<td>' . $valeur . '</td>';
                                    }
                                    */

                                    echo '<td>' . $article['id_article'] . '</td>';
                                    echo '<td>' . $article['titre'] . '</td>';
                                    echo '<td>' . $article['date_enregistrement'] . '</td>';
                                    echo '<td>' . substr($article['contenu'], 0, 7) . '...</td>';
                                    echo '<td><img src="' . $article['image_principale'] . '" style="width: 100px;" class="img-thumbnail"></td>';
                                    echo '<td>' . $article['titre_categorie'] . '</td>';
                                    // boutons pour les actions :
                                    echo '<td>';
                                    // bouton modifier
                                    echo '<a href="?action=modifier&id_article=' . $article['id_article'] . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a> ';
                                    // bouton supprimer
                                    echo '<a href="?action=supprimer&id_article=' . $article['id_article'] . '" onclick="return(confirm(\'Etes vous sûr ?\'))" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>';
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

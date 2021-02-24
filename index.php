<?php 
include 'inc/init.inc.php'; // connexion à la BDD + des outils
include 'inc/fonctions.inc.php'; // les fonctions utilisateur


// Récupération des catégories articles
$liste_categorie = $pdo->query("SELECT DISTINCT id_categorie, titre_categorie FROM categorie INNER JOIN relation_article_categorie USING (id_categorie) ORDER BY titre_categorie");

// Récupération des articles avec leur catégorie
// if else : si l'indice categorie existe dans $_GET  : requete prepare avec le where sur les categorie sinon la requete en query
// $liste_article = $pdo->query("SELECT id_article, titre, contenu, image_principale, titre_categorie FROM article INNER JOIN relation_article_categorie USING (id_article) INNER JOIN categorie USING (id_categorie) ORDER BY date_enregistrement DESC;");

if (isset($_GET['categorie'])) {
    $liste_article = $pdo->prepare("SELECT id_article, titre, contenu, image_principale, titre_categorie FROM article INNER JOIN relation_article_categorie USING (id_article) INNER JOIN categorie USING (id_categorie) WHERE id_categorie = :id_categorie ORDER BY date_enregistrement DESC");
    $liste_article->bindParam(':id_categorie', $_GET['categorie'], PDO::PARAM_STR);
    $liste_article->execute();
} else {
    $liste_article = $pdo->query("SELECT id_article, titre, contenu, image_principale, titre_categorie FROM article INNER JOIN relation_article_categorie USING (id_article) INNER JOIN categorie USING (id_categorie) ORDER BY date_enregistrement DESC");
}

// if( isset($_GET['categorie']) ) {
//     $liste_article = $pdo->prepare("SELECT id_article, titre, contenu, image_principale, titre_categorie FROM article INNER JOIN relation_article_categorie USING (id_article) INNER JOIN categorie USING (id_categorie) WHERE id_categorie = :id_categorie ORDER BY date_enregistrement DESC");
//     $liste_article->bindParam(':id_categorie', $_GET['categorie'], PDO::PARAM_STR);
//     $liste_article->execute();
// } else {
//     $liste_article = $pdo->query("SELECT id_article, titre, contenu, image_principale, titre_categorie FROM article INNER JOIN relation_article_categorie USING (id_article) INNER JOIN categorie USING (id_categorie) ORDER BY date_enregistrement DESC");
// }




// Début des affichages !
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
?>


        <main>
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <h1>Mon blog <i class="fas fa-ghost text-info"></i></h1><hr>
                        <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                    </div>                    
                </div>
                <div class="row mt-5">
                    <div class="col-sm-3">
                    <!-- 
                      Afficher la liste des catégories présentent dans $liste_categorie dans une liste ul li sous forme de <a href="">
                      exemple : <li><a href="?categorie=ID_CATEGORIE">TITRE_CATEGORIE</a></li>
                    -->
                    <ul class="list-group">
                        <li class="list-group-item bg-dark text-white">Catégories</li>
                        <li class="list-group-item"><a href="<?php echo URL; ?>">Tous les articles</a></li>

                        <?php                    
                            while($categorie = $liste_categorie->fetch(PDO::FETCH_ASSOC)) {

                                echo '<li class="list-group-item"><a href="?categorie=' . $categorie['id_categorie'] . '">' . $categorie['titre_categorie'] . '</a></li>';
                            }
                        ?>
                    </ul>
                    
                    </div>
                    <div class="col-sm-9">
                        <div class="row">

                            <?php 
                            
                            // une boucle while pour afficher les articles contenus dans la réponse de la BDD dans la variable $liste_article
                            while($article = $liste_article->fetch(PDO::FETCH_ASSOC)) {
                                // echo '<pre>'; var_dump($article);  echo '</pre><hr>';

                                echo '<div class="card" style="width: 48%; margin: 1%;">
                                        <img src="' . $article['image_principale'] . '" class="card-img-top" alt="' . $article['titre'] . '">
                                        <div class="card-body">
                                            <h5 class="card-title">' . $article['titre'] . '</h5>
                                            <p class="card-text"><b>Catégorie :</b> ' . $article['titre_categorie'] . '<hr>' . substr($article['contenu'], 0, 210) . ' ...</p>
                                            <a href="' . URL . 'page_article.php?id_article=' . $article['id_article'] . '" class="btn btn-primary">Lire la suite</a>
                                        </div>
                                    </div>';
                            }
                            
                            
                            ?>


                        </div>
                    </div>
                </div>
            </div>
        </main>


<?php
include 'inc/footer.inc.php';

<?php 
include 'inc/init.inc.php'; // connexion à la BDD + des outils
include 'inc/fonctions.inc.php'; // les fonctions utilisateur

// on vérifie l'existance de l'id_article dans l'url ($_GET) s'il existe on déclenche une requete de récupération sinon on redirige sur index. On vérifie aussi que l'id_article soit bien numérique avec la fonction prédéfinie is_numeric()
if( isset($_GET['id_article']) && is_numeric($_GET['id_article']) ) {

    $recup_article = $pdo->prepare("SELECT pseudo, nom, prenom, titre, image_principale, contenu, date_format(date_enregistrement, '%d/%m/%Y à %H:%i') AS date_fr, titre_categorie FROM article INNER JOIN membre USING(id_membre) INNER JOIN relation_article_categorie USING(id_article) INNER JOIN categorie USING(id_categorie) WHERE id_article = :id_article -- AND etat = 'public'");
    $recup_article->bindParam(':id_article', $_GET['id_article'], PDO::PARAM_STR);
    $recup_article->execute();

           /* 
    SELECT pseudo, nom, prenom, titre, image_principale, contenu, date_format(date_enregistrement, '%d/%m/%Y à %H:%i') AS date_fr, titre_categorie 
    FROM article 
    INNER JOIN membre USING(id_membre) 
    INNER JOIN relation_article_categorie USING(id_article)
    INNER JOIN categorie USING(id_categorie)
    WHERE id_article = 17 
    AND etat = 'public'
    */

    if(isset($_SESSION['membre']['id_membre']) && isset($_POST['message'])  ) {

    
        $message = trim($_POST['message']);
        $id_membre = trim($_SESSION['membre']['id_membre']);
        $id_article = trim($_GET['id_article']);
                            
        $enregistrement = $pdo->prepare("INSERT INTO commentaire (id_commentaire, id_membre, id_article, message, date_commentaire) VALUES( NULL, :id_membre, :id_article , :message, now())");
        $enregistrement->bindParam(':id_membre', $id_membre, PDO::PARAM_STR);
        $enregistrement->bindParam(':id_article', $id_article, PDO::PARAM_STR);
        $enregistrement->bindParam(':message', $message, PDO::PARAM_STR);
        $enregistrement->execute();   
        
    }   

} else {
    header('location:' . URL);
}


// on test si on a 1 ligne (si l'article existe en BDD)
if($recup_article->rowCount() < 1) {
    header('location:' . URL);
}

// on est sûr de n'avoir qu'une seule ligne car la requete se base sur un id (clé primaire)
// dans ce cas, pas de boucle, 1 fetch
$article = $recup_article->fetch(PDO::FETCH_ASSOC);

//-----------------------------------------------------------------------
  

// ---------------------------------COMMENTAIRE---------------------------

if( isset($_GET['id_article']) && is_numeric($_GET['id_article']) && !empty($_GET['id_article']) ) {

    $id_article = $_GET['id_article'];

    $liste_message = $pdo->prepare("SELECT * FROM commentaire WHERE id_article = :id_article ORDER BY date_commentaire DESC"); 
    $liste_message->bindParam(':id_article', $id_article, PDO::PARAM_STR);
    $liste_message->execute();
}

// ---------------------------------COMMENTAIRE---------------------------

// Début des affichages !
include 'inc/header.inc.php';
include 'inc/nav.inc.php';
// echo '<pre>'; var_dump($article); echo '</pre>';
?>
        

        <main>
            <div class="container">
                <div class="row">
                    
                        <div class="col-12">
                            <h1> <i class="fas fa-ghost text-info"></i>  <i class="fas fa-ghost text-info"></i>  <i class="fas fa-ghost text-info"></i>  <i class="fas fa-ghost text-info"></i>  <i class="fas fa-ghost text-info"></i> <?php echo $article['titre'] ?>  <i class="fas fa-ghost text-info"></i>  <i class="fas fa-ghost text-info"></i>  <i class="fas fa-ghost text-info"></i>  <i class="fas fa-ghost text-info"></i>  <i class="fas fa-ghost text-info"></i>  </h1><hr>
                            <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                        </div>                    
                    
                    <div class="row">
                        <div class="col-12">
                            <!-- 
                                - image (width 100% ( avec bootstrap : w-100))
                                - auteur - catégorie - date de création
                                - contenu 
                            -->                                                      
                            <img src="<?php echo $article['image_principale'] ?>" alt="article" class="w-100 img-thumbnail">
                            <br>
                            <p>Par: <b><?php echo $article['nom'] . ' ' . $article['prenom']; ?></b>. Catégorie : <b><?php echo $article['titre_categorie']; ?></b>. Le <?php echo $article['date_fr']; ?></p>
                            <hr>
                            <p><?php echo $article['contenu']; ?></p>
                        </div>
                    </div>
                </div>

                <!-- --------------------------------------------COMMENTAIRE---------------------------------- -->

                <div class="row">
                    <div class="conteneur">
                        <h1 class="bg-info m-auto text-white text-center p-4">Espace commentaire</h1>
                        <br><br><br>
                        <form method="post" action="" class="form_commentaire" id="form_commentaire">
                        <!-- <input type="hidden" name="id_article" value="<?php echo $id_article; ?>">   -->

                            
                            <textarea id="textarea_commentaire" name="message" id="message" cols="110" rows="10" placeholder="écrivez ici ......."></textarea>
                            <br>
                            <br>
                            <input type="submit" id="valider" value="Enregistrer" class="input_commentaire">                
                        </form >
                            <br>
                            <br>
                        <div>
                            <h2>Commentaires : 
                            
                            <?php


                            echo '( ' . $liste_message->rowCount() . ') messages;' ?></h2>
                            <hr>

                            <?php 
                            
                        
                            while($message = $liste_message->fetch(PDO::FETCH_ASSOC)) {

                                echo    '<div>
                                            <h5>' . $message['id_membre'] . ' ' . 'le ' . $message['date_commentaire'] . '</h5>
                                            <div>
                                                <p>' . $message['message'] . '</p>
                                            </div>
                                        </div>';

                            }
                                
                            

                            ?>
                        </div>
                        
                    </div>

        </main>


<?php
include 'inc/footer.inc.php';

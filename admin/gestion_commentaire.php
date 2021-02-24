<?php 
include '../inc/init.inc.php'; // connexion à la BDD + des outils
include '../inc/fonctions.inc.php'; // les fonctions utilisateur

//-----------------------------------------------------------
//-----------------------------------------------------------
// restriction d'accès
// si l'utilisateur n'est pas admin, on le redirige vers connexion.php
//-----------------------------------------------------------
//-----------------------------------------------------------


if( !user_is_admin()) {
    header("location:../connexion.php");
    exit();
}


//--------------SUPPRESSION DES COMMENTAIRES-----------------

if( isset($_GET['action']) && $_GET['action'] == 'supprimer' && !empty($_GET['id_commentaire']) )  {
    $suppression = $pdo->prepare("DELETE FROM commentaire WHERE id_commentaire = :id_commentaire");
    $suppression->bindParam(":id_commentaire", $_GET['id_commentaire'], PDO::PARAM_STR);
    $suppression->execute();
}

//--------------SUPPRESSION DES COMMENTAIRES-----------------




//------------------------------------------------------------------
//--------------------RECUPERATION EN BDD---------------------------

$id_article = '';
$id_membre = '';
$message = '';
$id_commentaire = '';

if( isset($_GET['action']) && $_GET['action'] == 'modifier' && !empty($_GET['id_commentaire']) )  {
    $recup_commentaire = $pdo->prepare("SELECT id_commentaire, id_article, message FROM commentaire WHERE id_commentaire = :id_commentaire");
    $recup_commentaire->bindParam(":id_commentaire", $_GET['id_commentaire'], PDO::PARAM_STR);
    $recup_commentaire->execute();

    if($recup_commentaire->rowCount() > 0) {
        $infos_commentaire = $recup_commentaire->fetch(PDO::FETCH_ASSOC);

        $id_article = $infos_commentaire['id_article'];
        $id_commentaire =  $infos_commentaire['id_commentaire'];
        $message =  $infos_commentaire['message'];
    }

}

//------------------------------------------------------------------
//--------------------RECUPERATION EN BDD---------------------------




//----------------MODIFICATION DES COMMENTAIRES---------------------

if(isset($_SESSION['membre']['id_membre']) && isset($_POST['message'])) {

    
    $message = trim($_POST['message']);
    $id_membre = trim($_SESSION['membre']['id_membre']);
                    
    $modification = $pdo->prepare("UPDATE commentaire SET message = :message WHERE id_commentaire = :id_commentaire");
    $modification->bindParam(':id_commentaire', $id_commentaire, PDO::PARAM_STR);
    $modification->bindParam(':message', $message, PDO::PARAM_STR);
    $modification->execute(); 
    
}  



$liste_message = $pdo->query("SELECT * FROM commentaire ORDER BY date_commentaire DESC"); 


// Début des affichages !
include '../inc/header.inc.php';
include '../inc/nav.inc.php';
// echo '<pre>'; var_dump($_POST); echo '</pre>';
?>


        <main>
            <div class="container">

                <div class="row">
                    <div class="col-12">
                        <h1>Gestion des commentaires <i class="fas fa-ghost text-info"></i></h1><hr>
                        <?php echo $msg; // on affiche la variable contenant les messages utilisateur ?>
                    </div>                    
                </div>
                <div class="row">
                        
                    <form method="post" action="" class="form_commentaire" id="form_commentaire">
                        <input type="hidden" name="id_commentaire" value="<?php echo $id_commentaire; ?>"> 
                        <input type="hidden" name="id_article" value="<?php echo $id_article; ?>">  
                        <input type="hidden" name="id_membre" value="<?php echo $id_membre; ?>">              
            
             
                        <textarea id="textarea_commentaire" name="message" id="message" cols="110" rows="10" placeholder="écrivez ici ...."><?php echo $message ?></textarea>
                        <br>
                        <br>
                        <input type="submit" id="valider" value="Modifier" class="input_commentaire">                
                    </form >
                </div>
                <div class="col-12">
                    <table class="table table-bordered mt-5">
                        <tr>
                            <th>N° commentaire</th>
                            <th>N° membre</th>
                            <th>N° article</th>
                            <th>message</th>
                            <th>date commentaire</th>  
                            <th>Action</th>     
                        </tr>

                        <?php 

                            while($message_commentaire = $liste_message->fetch(PDO::FETCH_ASSOC)) {
                                // var_dump($article);

                                echo '<tr>';

                                /*
                                foreach($article AS $valeur) {
                                    echo '<td>' . $valeur . '</td>';
                                }
                                */

                                echo '<td>' . $message_commentaire['id_commentaire'] . '</td>';
                                echo '<td>' . $message_commentaire['id_membre'] . '</td>';
                                echo '<td>' . $message_commentaire['id_article'] . '</td>';
                                echo '<td>' . substr($message_commentaire['message'], 0, 20) . '...</td>';
                                echo '<td>' . $message_commentaire['date_commentaire'] . '</td>';

                                // boutons pour les actions :
                                echo '<td>';
                                // bouton modifier
                                echo '<a href="?action=modifier&id_commentaire=' . $message_commentaire['id_commentaire'] . '" class="btn btn-warning btn-sm"><i class="fas fa-edit"></i></a> ';
                                // bouton supprimer
                                echo '<a href="?action=supprimer&id_commentaire=' . $message_commentaire['id_commentaire'] . '" onclick="return(confirm(\'Etes vous sûr ?\'))" class="btn btn-danger btn-sm"><i class="far fa-trash-alt"></i></a>';
                                echo '</td>';
                                

                                echo '</tr>';

                            }
                        
                        ?> 

                    </table>


                </div>
                                        

            </div>
        </main>


<?php
include '../inc/footer.inc.php';

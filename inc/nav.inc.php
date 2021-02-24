<nav class="navbar fixed-top navbar-expand-lg navbar-dark bg-dark" >
    <a class="navbar-brand" href="<?php echo URL; ?>index.php">Blog</a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav mr-auto">
            <li class="nav-item active">
                <a class="nav-link" href="<?php echo URL; ?>index.php">Accueil <span class="sr-only">(current)</span></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo URL; ?>contact.php">Contact</a>
            </li>

            <?php if( !user_is_connected() ) { ?>

            <li class="nav-item">
                <a class="nav-link" href="<?php echo URL; ?>connexion.php">Connexion</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo URL; ?>inscription.php">Inscription</a>
            </li>

            <?php  } else {  ?>

            <li class="nav-item">
                <a class="nav-link" href="<?php echo URL; ?>profil.php">Profil</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="<?php echo URL; ?>connexion.php?action=deconnexion">Se déconnecter</a>
            </li>

            <?php  } ?>

            <?php 
                // si l'utilisateur est admin, on lui affiche le menu d'administration
                if( user_is_admin() ) { 
            ?>
            
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Administration</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                    <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_article.php">Gestion articles</a>
                    <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_categorie.php">Gestion catégories</a>
                    <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_mot_cle.php">Gestion mots clés</a>
                    <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_membre.php">Gestion membres</a>
                    <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_avatar.php">Gestion avatars</a>
                    <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_commentaire.php">Gestion commentaire</a>
                </div>
            </li>

                    

            <?php } elseif( user_is_integrateur() ) {  
               // si l'utilisateur est integrateur, on lui accorde seulement la gestion des articles dans le menu d'administration   
            ?>

            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">Administration</a>
                <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                <a class="dropdown-item" href="<?php echo URL; ?>admin/gestion_article.php">Gestion articles</a>
                </div>
            </li>

            <?php } ?>
            
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <input class="form-control mr-sm-2" type="search" placeholder="Rechercher" aria-label="Rechercher">
            <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Rechercher</button>
        </form>
    </div>
</nav> 
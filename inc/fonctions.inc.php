<?php

// user_is_connected
// fonction permet de savoir si l'utilisateur est connecté;
function user_is_connected() {
    // si l'indice membre existe dans la session alors l'utilisateur est passé par la connexion et à donnée ses bonnes informations
    if(!empty($_SESSION['membre'])) {
        return true;
    } else {
        return false;
    }
}

// user_is_admin
// fonction permettant de savoir si l'utilisateur est admninistrateur
function user_is_admin() {
    if(user_is_connected() && $_SESSION['membre']['statut'] == 2) {
        return true;
    } else {
        return false;
    }
}

// user_is_integrateur
// fonction permettant de savoir si l'utilisateur est integrateur
function user_is_integrateur() {
    if( user_is_connected() && $_SESSION['membre']['statut'] == 3) {
        return true;
    }  else {
        return false;
    }
}



<?php

// CONNEXTION BDD
$host_dbname = 'mysql:host=localhost;dbname=projet_blog';
$login = 'root';
$password = '';
$options = array(
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_WARNING, 
                    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8');

$pdo = new PDO($host_dbname, $login, $password, $options);

// Création d'une variable vide pour afficher des messages utilisateur :
$msg = '';

// Création ou ouverture de la session ($_SESSION) notamment pour mettre les informations de connexion utilisateur (cela créer un cookie côté utilisateur et une session côté administrateur)
session_start();

// Création d'une constante contenant le chemin absolu d'accès au projet pour que nos liens de menu, d'appel css et js soit toujours corrects que l'on soit dans les pages frontoffice (site pour les utilisateurs / expérience utilisateur) ou dans les pages backoffice (site pour les admnistrateurs / expérience admnistrateur) 
// c'est différent de front-end et back-end, ici on parle de language
define ( 'URL','http://localhost/php/projet_blog/' );


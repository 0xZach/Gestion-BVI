<?php
/*
 * Description de c_auth.php
 *
 * auteur MASCLET Sylvain
 * Creation 26-01-2022
 * Derniere MAJ 25/02/2022
 * controleur de l'au/thentification
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ )$racine="..";


/* INCLUDES */

require_once "$racine/modele/DAO/AuthUsr_DAO.php";



/* INITIALISATION DES VARIABLES */

// variable qui permet d'aiguiller vers quelle page on veut aller
$fichier="vue/vue_auth";
$errMess="";





/* VÉRIFICATION DU MOT DE PASSE */

if(isset($_POST['confirmAuth'])){
    
    // on récupère le mot de passe
    $passW= filter_var($_POST['mdp'], FILTER_SANITIZE_STRING);
    
    if(AuthUsr_DAO::login($passW))
       $fichier="controleur/c_accueil";
    else{
        $errMess="Mot de passe incorrecte.";
    }
    
}

$titre="Authentification";
include_once "$racine/vue/entete.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
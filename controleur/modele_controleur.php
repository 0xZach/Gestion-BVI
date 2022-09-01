<?php
/*
 * Description de modele_controleur.php
 *
 * auteur NOM Prénom
 * Creation 26-01-2022
 * Derniere MAJ 25/02/2022
 * modèle pour la création d'un controleur
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}


/* INCLUDES */

require_once "$racine/modele/DAO/AuthUsr_DAO.php";



/* INITIALISATION DES VARIABLES */

$fichier=AuthUsr_DAO::isLoggedOn("vue/...");



/* CODE */



$titre="";
include "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
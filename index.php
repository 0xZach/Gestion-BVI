<?php
/*
 * Description de index.php
 *
 * auteur MASCLET Sylvain
 * Creation 26-01-2022
 * Derniere MAJ 31/01/2022
 * Fichier de démarrage de l'application web
 */

// affichage des erreurs
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'getRacine.php';
require_once "$racine/controleur/c_principal.php";

if (isset($_GET["action"])){
    $action = $_GET["action"];
}
else{
    
    $action = "defaut";
}

$fichier = controleurPrincipal($action);
include "$racine/controleur/$fichier.php";
?>


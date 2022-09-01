<?php
/*
 * Description de c_accueil.php
 *
 * auteur MASCLET Sylvain
 * Creation 28-01-2022
 * Derniere MAJ 28/02/2022
 * controleur de la page d'accueil
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ )$racine="..";



/* INCLUDES */

require_once "$racine/modele/DAO/AuthUsr_DAO.php";
require_once "$racine/modele/func.inc.php";



/* INITIALISATION DES VARIABLES */

$fichier = AuthUsr_DAO::isLoggedOn('vue/vue_accueil');

//var_dump(intervalDate("aujourd'hui"));
//echo "<br>";
//var_dump(intervalDate("hier"));
//echo "<br>";
//var_dump(intervalDate("mois courant"));
//echo "<br>";
//var_dump(intervalDate("mois dernier"));
//echo "<br>";
//var_dump(intervalDate("annee courante"));
//echo "<br>";
//var_dump(intervalDate("annee precedente"));

//if(isset($_POST['importCSV'])){
//    import_CSV();
//}



$titre="Accueil";
include_once "$racine/vue/entete.html.php";
if($fichier !== 'controleur/c_auth')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";

?>



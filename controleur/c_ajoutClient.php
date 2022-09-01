<?php
/*
 * Description de c_ajoutClient.php
 *
 * auteur MASCLET Sylvain
 * Creation 26-01-2022
 * Derniere MAJ 28/02/2022
 * controleur de l'ajout des clients
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}


/* INCLUDES */

require_once "$racine/modele/DAO/Client_DAO.php";
require_once "$racine/modele/DAO/AuthUsr_DAO.php";



/* INITIALISATION DES VARIABLES */

$errMess = "";
$validMess = "";
$validAjout = true;
$infosClient = array();
$clientAccess = new Client_DAO();
$fichier = AuthUsr_DAO::isLoggedOn('vue/vue_ajoutClient');




/* ACTION LORSQUE L'ON SOUHAITE AJOUTER UN CLIENT */

if(isset($_POST["confirmAjout"])){
    if($_POST['nom'] !== ""){
        
        
        /* RÉCUPÉRATION DES VALEURS DES REQUÈTES POST */
        
        $infosClient['nom']=filter_var($_POST['nom'], FILTER_SANITIZE_STRING);
        $infosClient['adresse']=filter_var($_POST['adresse'], FILTER_SANITIZE_STRING);
        $infosClient['fix']=str_replace(' ','',filter_var($_POST['fix'], FILTER_SANITIZE_STRING));
        $infosClient['mobile']=str_replace(' ','',filter_var($_POST['mobile'], FILTER_SANITIZE_STRING));
        $infosClient['mel']=filter_var($_POST['mel'], FILTER_SANITIZE_STRING);
        $infosClient['ville']=filter_var($_POST['ville'], FILTER_SANITIZE_STRING);
        $infosClient['codepostal']= filter_var($_POST['codepostal'], FILTER_SANITIZE_STRING);
        
        // ajout d'un 0 au début pour les code postaux en dessous de 10 000:
        // exemple:
        // si l'utilisateur veut ajouter Nice avec le code postal 6000
        // alors le code rajoutera un 0 devant pour obtenir 06000
        if(intval($infosClient['codepostal']) < 10000 && strlen($infosClient['codepostal']) === 4){
            $infosClient['codepostal'] = '0' . $infosClient['codepostal'];
        }
        
        
        
        
        /* MESSAGES D'ÉRREUR TÉLÉPHONE */
        
        // on ne check que s'il y a 1 ou plusieurs caractères car is_numeric renvois faux pour un string vide
        if(strlen($infosClient['fix']) > 0 && !is_numeric($infosClient['fix'])){
            $errMess = "le numéro de téléphone fix doit être composé uniquement de chiffres.";
            $validAjout = false;
        }
        if(strlen($infosClient['mobile']) > 0 && !is_numeric($infosClient['mobile'])){
            $errMess = "le numéro de téléphone mobile doit être composé uniquement de chiffre.";
            $validAjout = false;
        }
        
        
        
        /* MESSAGES D'ÉRREUR CODE POSTAL */
        
        if(strlen($infosClient['codepostal']) < 0 && !is_numeric($infosClient['codepostal'])){
            $errMess = "le code postal ne peut être composé que de chiffres.";
            $validAjout = false;
        }
        if((strlen($infosClient['codepostal']) < 4 || strlen($infosClient['codepostal']) > 5)&& strlen($infosClient['codepostal']) > 0){
            $errMess = "le code postal n'est composé que de 4 ou 5 chiffres.";
            $validAjout = false;
        }
        
        
        
        /* ENVOI DES INFOS POUR L'AJOUT D'UN CLIENT */
        
        if($validAjout){
            $clientAccess->ajoutClient($infosClient);
            $validMess = "Client ajouté";
        }
        
        
        
    }
    else
    {
        $errMess = "Le champs Nom doit être obligatoirement rempli.";
    }

}



$titre="Ajout d'un Client";
include_once "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include_once "$racine/vue/liste.html.php";
include_once "$racine/$fichier.php";
include_once "$racine/vue/pied.html.php";
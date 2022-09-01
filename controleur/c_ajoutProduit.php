<?php
/*
 * Description de c_ajoutProduit.php
 *
 * auteur MASCLET Sylvain
 * Creation 07-02-2022
 * Derniere MAJ 07/03/2022
 * controleur de l'ajout des Produits
 * 
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}


/* INCLUDES */

require_once "$racine/modele/DAO/Produit_DAO.php";
require_once "$racine/modele/DAO/AuthUsr_DAO.php";




/* INITIALISATION DES VARIABLES */

$errMess = "";
$validMess = "";
$fichier = AuthUsr_DAO::isLoggedOn("vue/vue_ajoutProduit");
$validAjout = true;
$prodAccess = new Produit_DAO();
$infosProduit = array();




/* ACTION LORSQUE L'ON SOUHAITE AJOUTER UN CLIENT */

if(isset($_POST["confirmAjout"])){
    if($_POST['libelle'] !== ""){
        
        
        
        /* RÉCUPÉRATION DES VALEURS DES REQUÈTES POST */
        
        $infosProduit['libelle']=filter_var($_POST['libelle'], FILTER_SANITIZE_STRING);
        
        if(isset($_POST['code_barre']))
            $infosProduit['code_barre']=filter_var($_POST['code_barre'], FILTER_SANITIZE_STRING);
               
        
        // le prix est toujours en TTC de base
        if($_POST['prix'] !== "" && is_numeric($_POST['prix']))
            $infosProduit['pu_ht']=number_format(floatval(filter_var($_POST['prix'], FILTER_SANITIZE_STRING))/1.2,2,'.','');
        else
            $infosProduit['pu_ht']="0.0";


        if(isset($_POST['tx_horaire']))
            $infosProduit['tx_horaire']= true;
        else
            $infosProduit['tx_horaire']= false;

        
        
        
        /* ENVOI DES INFOS POUR L'AJOUT D'UN CLIENT */
        
        if($validAjout){
            if($_POST['radioProduit'] === 'service')
                $prodAccess->ajoutService($infosProduit);
            else
                $prodAccess->ajoutBien($infosProduit);
            
            $validMess = "Produit ajouté";
        }
        
        
    }
    else
    {
        $errMess = "Le champs Nom doit être obligatoirement rempli.";
    }

}



$titre="Ajout d'un Produit";
include "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
<?php
/*
 * Description de c_ajoutDevis.php
 *
 * auteur MASCLET Sylvain
 * Creation 11-02-2022
 * Derniere MAJ 02/03/2022
 * controleur de l'ajout des Devis
 * 
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}


/* INCLUDES */

require_once "$racine/modele/DAO/Client_DAO.php";
require_once "$racine/modele/DAO/Produit_DAO.php";
require_once "$racine/modele/DAO/Devis_DAO.php";
require_once "$racine/modele/DAO/AuthUsr_DAO.php";




/* INITIALISATION DES VARIABLES */

$validMess = "";

$fichier = AuthUsr_DAO::isLoggedOn('vue/vue_ajoutDevis');

$clientAccess = new Client_DAO();
$produitAccess = new Produit_DAO();
$devisAccess = new Devis_DAO();

$infosDevis = array();
$lesProduits = array();
$validAjout = True;

$clientSelect = '';
$produitSelect = '';


$lesClients = array();
$lesProduits = array();





/* ACTION LORSQUE L'ON SOUHAITE AJOUTER UN DEVIS */

if(isset($_POST['confirmAjout'])){
    
    
    
    /* RÉCUPÉRATION DES VALEURS DES REQUÈTES POST */
    
    // on récupère le client choisie
    $idClient = filter_var($_POST['idClient'],FILTER_SANITIZE_STRING);
    if($clientAccess->getLesClients($idClient) === array()){
        $validAjout = false;
    }

    
    
    // on récupère les produits choisi en fonction de leur id récupéré par les clefs de $_POST
    foreach($_POST as $key => $value){
        if(strstr($key, 'PR') !== false){
            $lesProduits[] = $produitAccess->getProduits($key)[0];
        }
    }

    
    
    // calcul du montant net à payer du devis (net a payer = ttc)
    $montant_ttc = 0.0;
    for($i=0;$i<count($lesProduits);$i++){
        $montant_ttc += $lesProduits[$i]->getAttribut("ttc");
    }
    $montant_ttc = $montant_ttc;
    
    
    
    
    /* AJOUT DU DEVIS */
    
    if($validAjout){
        
        
        // on complète le tableau des infos du devis
        $infosDevis['dateCreation'] = filter_var($_POST['dateCreation'], FILTER_SANITIZE_STRING);
        $infosDevis['date_valid_fin'] = filter_var($_POST['dateFin'], FILTER_SANITIZE_STRING);
        $infosDevis['net_apayer'] = $montant_ttc;
        $infosDevis['etat'] = filter_var($_POST['radioEtat'],FILTER_SANITIZE_STRING);
        $infosDevis['id_cli'] = $idClient;
        
        
        
        // création du nouveau devis (on récupère l'id pour pouvoir l'utiliser après)
        $nouvId=$devisAccess->ajoutDevis($infosDevis);
        
        if($lesProduits !== array()){
            // on créé les liens entre les produits choisi et la devis créée
            $devisAccess->ajoutDeviser($devisAccess->getLesDevis($nouvId)[0], $lesProduits);
        }
        
        $validMess = "Devis ajouté";
    }

    
    

}




/* SELECT DU CHOIX DU CLIENT */

$lesClients = $clientAccess->getLesClients();
$clientSelect = '<select form="ajoutFact" name="nomClient" form="">';
for($i=0;$i<count($lesClients);$i++){
    $clientSelect .= '<option id="'.$lesClients[$i]->getAttribut('id').'">'.$lesClients[$i]->getAttribut('nom').'</option>';
}
$clientSelect .= '</select>';




/* SELECT DU CHOIX DES PRODUITS */

$lesProduits = $produitAccess->getProduits();
$produitSelect = '<select name="nomProduit">';
for($i=0;$i<count($lesProduits);$i++){
    if($lesProduits[$i]->getAttribut('id') !== "PR00000")
        $produitSelect .= '<option id="'.$lesProduits[$i]->getAttribut('id').'">'.$lesProduits[$i]->getAttribut('libelle').'</option>';
}
$produitSelect .= '</select>';






$titre="Ajout d'un Devis";
include_once "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
?>
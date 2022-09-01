<?php
/*
 * Description de c_ajoutFacture.php
 *
 * auteur MASCLET Sylvain
 * Creation 11-02-2022
 * Derniere MAJ 02/03/2022
 * controleur de l'ajout des Factures
 * 
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}



/* INCLUDES */

require_once "$racine/modele/DAO/Client_DAO.php";
require_once "$racine/modele/DAO/Produit_DAO.php";
require_once "$racine/modele/DAO/Facture_DAO.php";
require_once "$racine/modele/DAO/Echeancier_DAO.php";
require_once "$racine/modele/DAO/AuthUsr_DAO.php";




/* INITIALISATION DES VARIABLES */

$validMess = "";

$fichier = AuthUsr_DAO::isLoggedOn("vue/vue_ajoutFacture");

$clientAccess = new Client_DAO();
$produitAccess = new Produit_DAO();
$factureAccess = new Facture_DAO();
$echeanceAccess = new Echeancier_DAO();

$lesProduits = array();
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
        if(strstr($key, 'PR') !== false)
            $lesProduits[] = $produitAccess->getProduits($key)[0];
    }
    
    // calcul du montant ht de la facture
    $montant_ht = 0.0;
    for($i=0;$i<count($lesProduits);$i++){
        $montant_ht += $lesProduits[$i]->getAttribut('ht');
    }
    $ttc = $montant_ht * 1.2;

    
    
    
    /* AJOUT DE LA FACTURE */
    
    if($validAjout){
        
        // on complète le tableau des infos de la facture
        $infos['dateCreation'] = filter_var($_POST['dateCreation'], FILTER_SANITIZE_STRING);
        $infos['mtnt_ht'] = $montant_ht;
        $infos['accompte'] = filter_var($_POST['accompte'], FILTER_SANITIZE_STRING);
        $infos['etat'] = filter_var($_POST['radioEtat'],FILTER_SANITIZE_STRING);
        $infos['id_cli'] = $idClient;
        $infos['moyen_paiement'] = filter_var($_POST['moyen_paiement'],FILTER_SANITIZE_STRING);
        
        
        // création de la nouvelle facture (on récupère l'id pour pouvoir l'utiliser après
        $nouvId=$factureAccess->ajoutFacture($infos);


        if($lesProduits !== array()){
            // on créé les liens entre les produits choisi et la facture créée
            $factureAccess->ajoutFacturation($factureAccess->getLesFactures($nouvId)[0], $lesProduits);
        }
        
        $infosEcheance = array(
            'id_facture' => $nouvId,
            'reste_apayer' => $ttc-$infos['accompte']
        );
        
        $echeanceAccess->ajoutEcheance($infosEcheance);
        
        
        
        $validMess = "Facture ajoutée";
    }

}





/* SELECT DU CHOIX DU CLIENT */

$lesClients = $clientAccess->getLesClients();
$clientSelect = '<select form="ajoutFact" name="nomClient">';
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






$titre="Ajout d'une Facture";
include_once "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
?>
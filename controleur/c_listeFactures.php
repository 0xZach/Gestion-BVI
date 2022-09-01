<?php
/*
 * Description de c_modifFactures.php
 *
 * auteur MASCLET Sylvain
 * Creation 15-02-2022
 * Derniere MAJ 08/03/2022
 * controleur de l'affichage des Factures
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}




/* INCLUDES */

require_once "$racine/modele/DAO/Facture_DAO.php";
require_once "$racine/modele/DAO/Devis_DAO.php";
require_once "$racine/modele/DAO/AuthUsr_DAO.php";




/* INITIALISATION DES VARIABLES */

$fichier= AuthUsr_DAO::isLoggedOn("vue/vue_listeFactures");

$factAccess = new Facture_DAO();
$devisAccess = new Devis_DAO();

$lesFactures = array();
$champsFactures=array('id','creation','client','ttc','accompte','net_apayer');
$tabRows='';
$ordre = '';
$interval = array('','');




/* TRANSFORMATION D'UN DEVIS EN FACTURE */

if(isset($_GET['ledevis'])){
    $idDevis = filter_var($_GET['ledevis'],FILTER_SANITIZE_STRING);
    $leDevis = $devisAccess->getLesDevis($idDevis)[0];
    
    if($leDevis->getAttribut('etat') === 'DE')
        $devisAccess->modifDevis($idDevis, 'etat','DV');
    
    $lesFactures = array($devisAccess->fromDevToFact($idDevis));
}






/* SUPPRESSION D'UNE FACTURE */

if(isset($_GET['tosuppr'])){
    // on récupère la facture envoyé depuis c_modifUneFacture et on la supprime
    $factAccess->supprFacture(filter_var($_GET['tosuppr']));
}




/* RECHERCHE D'UNE FACTURE */

if(isset($_POST['recherche'])){
    
    $paramRecherche= filter_var($_POST['recherche'], FILTER_SANITIZE_STRING);
    
    if ($_POST['intervalDebut'] === "" || $_POST['intervalFin'] === ""){
        
        $lesFactures = $factAccess->getLesFactures($paramRecherche);
    }
    else
    {
        $interval = array(filter_var($_POST['intervalDebut'], FILTER_SANITIZE_STRING),filter_var($_POST['intervalFin'], FILTER_SANITIZE_STRING));
        
        $lesFactures = $factAccess->getLesFactures($paramRecherche, $interval);
    }
    
    
    
}




/* REMPLISSAGE DE LA VARIABLE D'AFFICHAGE DES FACTURES */

if($lesFactures !== array()){

    for($i=0;$i<sizeof($lesFactures);$i++){
        
        
        $tabRows .= '<tr class="factureclick">';
        
        $tabRows .= '<td><input form="exportListe" class="checkExport" type="checkbox" name="'.$lesFactures[$i]->getAttribut('id').'" value=""></td>';

        
        $onclick = 'onclick="window.location.href=\'?action=lafacture&goto='.$lesFactures[$i]->getAttribut('id').'\'"';
        
        for($j=0;$j<sizeof($champsFactures);$j++){
            
            $tabRows.='<td colspan="2" '.$onclick.'>';

            $idFacture=$champsFactures[$j].'@'.$lesFactures[$i]->getAttribut('id');

            // switch pour vérifier la valeur à afficher
            switch($champsFactures[$j]){
                case 'client':
                    $tabRows .= '<p id="'.$idFacture.'">' . $lesFactures[$i]->getAttribut($champsFactures[$j])->getAttribut('nom') . '</p>';
                    break;
                default:
                    $tabRows .= '<p id="'.$idFacture.'">' . $lesFactures[$i]->getAttribut($champsFactures[$j]) . '</p>';
            }
            $tabRows .= '</td>';
        }
        if($lesFactures[$i]->getEtat() === 'FE')
            $tabRows .= '<td><a class="btn btn_nav suppr" href="?action=lesfactures&tosuppr='.$lesFactures[$i]->getAttribut('id').'"><i class="fa-solid fa-trash"></i></a></td>';
        else
            $tabRows .= '<td><p>Facture validée</p></td>';
        $tabRows .= '</tr>';
    }

}


$titre="Liste des Factures";
include "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
?>
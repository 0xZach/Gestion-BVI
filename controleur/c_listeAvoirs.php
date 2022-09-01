<?php
/*
 * Description de c_listeAvoirs.php
 *
 * auteur MASCLET Sylvain
 * Creation 08-03-2022
 * Derniere MAJ 08/03/2022
 * controleur de l'affichage des avoirs
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}


/* INCLUDES */

require_once "$racine/modele/DAO/AuthUsr_DAO.php";
require_once "$racine/modele/DAO/Facture_DAO.php";
require_once "$racine/modele/DAO/Echeancier_DAO.php";



/* INITIALISATION DES VARIABLES */

$fichier=AuthUsr_DAO::isLoggedOn("vue/vue_listeAvoirs");
$factAccess = new Facture_DAO();
$echeanceAccess = new Echeancier_DAO();
$lesAvoirs = array();
$champsAvoirs=array('avoir','raison','facture','client','ttc','rembourser','description');
$tabRows="";
$interval = array('','');




/* TRANSFORMATION D'UNE FACTURE EN AVOIR */

if(isset($_POST['confirmAvoir'])){
    $infosAvoir = array(
        'id_facture' => filter_var($_GET['laFacture'],FILTER_SANITIZE_STRING),
        'raison' => filter_var($_POST['raison'],FILTER_SANITIZE_STRING),
        'remboursement' => filter_var($_POST['rembourser'],FILTER_SANITIZE_STRING),
        'description' => filter_var($_POST['description'],FILTER_SANITIZE_STRING)
    );
    
    $factAccess->modifFacture(filter_var($_GET['laFacture'],FILTER_SANITIZE_STRING), 'etat', 'FV');
    $nouvAvoirId = $factAccess->ajoutAvoir($infosAvoir);
    $lesAvoirs = $factAccess->getLesAvoirs($nouvAvoirId);

        $infosEcheance = array(
        'id_facture' => $nouvAvoirId,
        'reste_apayer' => $infosAvoir['remboursement']
    );

    $echeanceAccess->ajoutEcheance($infosEcheance);
}








/* MODIFICATION D'UN AVOIR */

if(isset($_GET['id']) && isset($_GET['value'])){
    
    // on découpe le string id 
    // car il est composé du nom de champ d'un produit de son id et de son type (bien ou service)
    $lesValeurs = explode('@',filter_var($_GET['id'],FILTER_SANITIZE_STRING));
    $nouvValeur = filter_var($_GET['value'],FILTER_SANITIZE_STRING);
    
    // modification du produit
    $factAccess->modifAvoir($lesValeurs[1], $lesValeurs[0], $nouvValeur);
    
    // on re récupère les produits après la modification
    $lesAvoirs = $factAccess->getLesAvoirs();
      
}




/* RECHERCHE D'UN PRODUIT */

// si on est en mode recherche, on n'affiche que les produits liés aux valeurs recherchées
if(isset($_POST['recherche'])){
    $paramRecherche= filter_var($_POST['recherche'], FILTER_SANITIZE_STRING);
    
    if ($_POST['intervalDebut'] === "" || $_POST['intervalFin'] === ""){
        
        $lesAvoirs = $factAccess->getLesAvoirs($paramRecherche);
    }
    else
    {
        $interval = array(filter_var($_POST['intervalDebut'], FILTER_SANITIZE_STRING),filter_var($_POST['intervalFin'], FILTER_SANITIZE_STRING));
        
        $lesAvoirs = $factAccess->getLesAvoirs($paramRecherche, $interval);
    }
}




/* REMPLISSAGE DE LA VARIABLE D'AFFICHAGE DES DEVIS */

if($lesAvoirs !== array()){

    for($i=0;$i<sizeof($lesAvoirs);$i++){
        $tabRows.='<tr>';

        $tabRows .= '<td><input form="exportListe" class="checkExport" type="checkbox" name="'.$lesAvoirs[$i]->getAttribut('id').'" value=""></td>';

        
        for($j=0;$j<sizeof($champsAvoirs);$j++){
            
            $tabRows.='<td colspan="2">';

            $idAvoir=$champsAvoirs[$j].'@'.$lesAvoirs[$i]->getAttribut('id');

            // switch pour vérifier la valeur à afficher
            switch($champsAvoirs[$j]){
                case 'avoir':
                    $tabRows.= '<p>'.$lesAvoirs[$i]->getAttribut('id').'</p>';
                    break;
                
                case 'facture':
                    $tabRows.='<p>'.$lesAvoirs[$i]->getAttribut('facture')->getAttribut('id').'</p>';
                    break;
                
                case 'ttc':
                    $tabRows.='<p>'.$lesAvoirs[$i]->getAttribut('facture')->getAttribut('ttc').'</p>';
                    break;
                
                case 'client':
                    $tabRows.='<p>'.$lesAvoirs[$i]->getAttribut('facture')->getAttribut('client')->getAttribut('nom').'</p>';
                    break;
                
                default:
                    $tabRows.='<input class="infos" id="'.$idAvoir.'" type="text" size="10" value="';

                    // switch pour vérifier la valeur à afficher
                    $tabRows .= $lesAvoirs[$i]->getAttribut($champsAvoirs[$j]);
                    $tabRows .= '">';
                    break;
            }
            $tabRows .= '</td>';
        }
        // bouton d'export au format pdf avec une requête get renvoyant l'id de l'avoir
        $tabRows .= '<td>'
                  . '<a href="?action=pdffacture&laFacture='.$lesAvoirs[$i]->getAttribut('id').'&type=avoir" '
                  . 'target="_blank">'
                  . '<i class="fa-solid fa-arrow-up-from-bracket"></i>'
                  . '</a>'
                  . '</td>';
        
        $tabRows .= '</tr>';
    }

}



$titre="Liste des Avoirs";
include "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";

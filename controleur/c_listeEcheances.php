<?php
/*
 * Description de c_listeEcheances.php
 *
 * auteur MASCLET Sylvain
 * Creation 10-03-2022
 * Derniere MAJ 10/03/2022
 * controleur de l'affichage des Echeances
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}


/* INCLUDES */

require_once "$racine/modele/DAO/AuthUsr_DAO.php";
require_once "$racine/modele/DAO/Echeancier_DAO.php";
require_once "$racine/modele/DAO/Facture_DAO.php";



/* INITIALISATION DES VARIABLES */

$fichier=AuthUsr_DAO::isLoggedOn("vue/vue_listeEcheances");
$factAccess = new Facture_DAO();
$echeanceAccess = new Echeancier_DAO();
$lesEcheances = array();
$champsEcheances=array('facture','echeance','client','ttc','moyen','reste_apayer','dateValidation');
$tabRows="";
$interval = array('','');








/* MODIFICATION D'UN AVOIR */

if(isset($_GET['id']) && isset($_GET['value'])){
    
    // on découpe le string id 
    // car il est composé du nom de champ d'un produit de son id et de son type (bien ou service)
    $lesValeurs = explode('@',filter_var($_GET['id'],FILTER_SANITIZE_STRING));
    $nouvValeur = filter_var($_GET['value'],FILTER_SANITIZE_STRING);
    
    switch($lesValeurs[0]){
        case 'moyen_paiement':
            $factAccess->modifFacture($lesValeurs[1], $lesValeurs[0], $nouvValeur);
            break;
        case 'dateValidation':
            $echeanceAccess->modifDateValidation($lesValeurs[1], $nouvValeur);
            break;
        default:
            $echeanceAccess->modifEcheance($lesValeurs[1], $lesValeurs[0], $nouvValeur);
            break;
    }
    
    // on re récupère la liste après modification
    $lesEcheances = $echeanceAccess->getLesEcheances();
      
}




/* RECHERCHE D'UN PRODUIT */

// si on est en mode recherche, on n'affiche que les produits liés aux valeurs recherchées
if(isset($_POST['recherche'])){
    $paramRecherche= filter_var($_POST['recherche'], FILTER_SANITIZE_STRING);
    
    if ($_POST['intervalDebut'] === "" || $_POST['intervalFin'] === ""){
        
        $lesEcheances = $echeanceAccess->getLesEcheances($paramRecherche);
    }
    else
    {
        $interval = array(filter_var($_POST['intervalDebut'], FILTER_SANITIZE_STRING),filter_var($_POST['intervalFin'], FILTER_SANITIZE_STRING));
        $lesEcheances = $echeanceAccess->getLesEcheances($paramRecherche, $interval);
    }
}




/* REMPLISSAGE DE LA VARIABLE D'AFFICHAGE DES DEVIS */

if($lesEcheances !== array()){
    
    for($i=0;$i<sizeof($lesEcheances);$i++){
        if($lesEcheances[$i]->getAttribut('etat') === 'EE'){
            $tabRows.='<tr>';

            $tabRows .= '<td><input form="exportNonValid" class="checkExport" type="checkbox" name="'.$lesEcheances[$i]->getAttribut('id').'" value=""></td>';


            for($j=0;$j<sizeof($champsEcheances);$j++){

                $tabRows.='<td colspan="2">';

                $idEcheance=$champsEcheances[$j].'@'.$lesEcheances[$i]->getAttribut('id');
                $laFacture = $lesEcheances[$i]->getAttribut('facture');

                // switch pour vérifier la valeur à afficher
                switch($champsEcheances[$j]){
                    case 'echeance':
                    case 'ttc':
                        $tabRows .= '<p>'.$laFacture->getAttribut($champsEcheances[$j]).'</p>';
                        break;

                    case 'moyen':
                        $tabRows .= '<input class="infos" id="moyen_paiement@'.$laFacture->getAttribut('id').'" type="text" size="10" value="';
                        $tabRows .= $laFacture->getAttribut($champsEcheances[$j]);
                        $tabRows .= '">';
                        break;

                    case 'facture':
                        $tabRows .= '<p>'.$lesEcheances[$i]->getAttribut($champsEcheances[$j])->getAttribut('id').'</p>';
                        break;

                    case 'client':
                        $tabRows .= '<p>'.$laFacture->getAttribut($champsEcheances[$j])->getAttribut('nom').'</p>';
                        break;

                    case 'dateValidation':
                        $tabRows .= '<input class="infos" id="'.$idEcheance.'" type="date" size="10" value="';
                        $tabRows .= $lesEcheances[$i]->getAttribut($champsEcheances[$j]);
                        $tabRows .= '">';
                        break;

                    default:
                        $tabRows .= '<input class="infos" id="'.$idEcheance.'" type="text" size="10" value="';
                        $tabRows .= $lesEcheances[$i]->getAttribut($champsEcheances[$j]);
                        $tabRows .= '">';
                        break;
                }
                $tabRows .= '</td>';
            }
        }
        
        $tabRows .= '</tr>';
    }

}



$titre="Liste des Échéances";
include "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
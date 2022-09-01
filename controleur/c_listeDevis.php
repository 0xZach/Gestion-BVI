    <?php
/*
 * Description de c_modifDevis.php
 *
 * auteur MASCLET Sylvain
 * Creation 18-02-2022
 * Derniere MAJ 28/02/2022
 * controleur de l'affichage des Devis
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}




/* INCLUDES */

require_once "$racine/modele/DAO/Devis_DAO.php";
require_once "$racine/modele/DAO/AuthUsr_DAO.php";




/* INITIALISATION DES VARIABLES */

$fichier= AuthUsr_DAO::isLoggedOn("vue/vue_listeDevis");
$devisAccess = new Devis_DAO();


$lesDevis = array();
$champsDevis=array('id','creation','fin','client','net_apayer');
$tabRows='';
$interval = array('','');





/* SUPPRESSION D'UN DEVIS */

if(isset($_GET['tosuppr'])){
    $devisAccess->supprDevis(filter_var($_GET['tosuppr']));
    $lesDevis = $devisAccess->getLesDevis();
}





/* RECHERCHE D'UN DEVIS */

// si on est en mode recherche, on n'affiche que les devis liés aux valeurs recherchées
if(isset($_POST['recherche'])){
    $paramRecherche= filter_var($_POST['recherche'], FILTER_SANITIZE_STRING);
    
    if ($_POST['intervalDebut'] === "" || $_POST['intervalFin'] === ""){
        
        $lesDevis = $devisAccess->getLesDevis($paramRecherche);
    }
    else
    {
        $interval = array(filter_var($_POST['intervalDebut'], FILTER_SANITIZE_STRING),filter_var($_POST['intervalFin'], FILTER_SANITIZE_STRING));
        
        $lesDevis = $devisAccess->getLesDevis($paramRecherche, $interval);
    }
}




/* REMPLISSAGE DE LA VARIABLE D'AFFICHAGE DES DEVIS */

if($lesDevis !== array()){
    
    for($i=0;$i<sizeof($lesDevis);$i++){
        
        
        $tabRows.='<tr class="devisclick">';
        
        
        $tabRows .= '<td><input form="exportListe" class="checkExport" type="checkbox" name="'.$lesDevis[$i]->getAttribut('id').'" value=""></td>';

        
        
        $onclick = 'onclick="window.location.href=\'?action=ledevis&goto='.$lesDevis[$i]->getAttribut('id').'\'"';
        
        for($j=0;$j<sizeof($champsDevis);$j++){

            $tabRows.='<td colspan="2" '.$onclick.'>';

            $idDevis=$champsDevis[$j].'@'.$lesDevis[$i]->getAttribut('id');

            // switch pour vérifier la valeur à afficher
            switch($champsDevis[$j]){
                case 'client':
                    $tabRows .= '<p id="'.$idDevis.'">' . $lesDevis[$i]->getAttribut($champsDevis[$j])->getAttribut('nom') . '</p>';
                    break;
                default:
                    $tabRows .= '<p id="'.$idDevis.'">' . $lesDevis[$i]->getAttribut($champsDevis[$j]) . '</p>';
            }
            $tabRows .= '</td>';
        }
        if($lesDevis[$i]->getEtat() === 'DE')
            $tabRows .= '<td><a class="btn btn_nav suppr" href="?action=lesdevis&tosuppr='.$lesDevis[$i]->getAttribut('id').'"><i class="fa-solid fa-trash"></i></a></td>';
        else
            $tabRows .= '<td><p>Devis validé</p></td>';
        
        
        
        $tabRows .= '</tr>';
    }
}



$titre="Liste des Devis";
include "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
?>
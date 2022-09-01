 <?php
/*
 * Description de c_pdfEcheancier.php
 *
 * auteur MASCLET Sylvain
 * Creation 11-03-2022
 * Derniere MAJ 11/03/2022
 * controleur de la page d'export des échéances
 */
if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}

require_once "$racine/modele/classes/PDF.php";
require_once "$racine/modele/DAO/Echeancier_DAO.php";

$echeanceAccess = new Echeancier_DAO();
$lesEcheances = array();
$pdf = new PDF();

if(isset($_GET['export'])){
    foreach($_POST as $key => $value){
        if(strstr($key, 'EC')){
            $lecheance = $echeanceAccess->getLesEcheances(filter_var($key,FILTER_SANITIZE_STRING))[0];
            $lesEcheances[] = $lecheance;
        }
    }
    $interval = explode('@',$_GET['interval']);
}   


$pdf->newPage($lesEcheances, null,array(), $interval);


$pdf->displayPDF();

?>
 <?php
/*
 * Description de c_pdfDevis.php
 *
 * auteur MASCLET Sylvain
 * Creation 18-02-2022
 * Derniere MAJ 18/02/2022
 * controleur de la page d'export des devis
 */
if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}

require_once "$racine/modele/classes/PDF.php";
require_once "$racine/modele/DAO/Devis_DAO.php";
require_once "$racine/modele/DAO/Client_DAO.php";

$clientAccess = new Client_DAO();
$devisAccess = new Devis_DAO();
$lesDeviser = array();
$pdf = new PDF();



if(isset($_GET['export'])){
    foreach($_POST as $key => $value){
        if(strstr($key, 'DE')){
            $leDevis = $devisAccess->getLesDevis($key)[0];
            $lesDeviser = $devisAccess->getDeviser($leDevis->getAttribut('id'));
            $pdf->newPage($leDevis,$leDevis->getAttribut('client'),$lesDeviser);
        }
    }
}
else
{
    $idDevis = filter_var($_GET['leDevis'],FILTER_SANITIZE_STRING);

    $leDevis = $devisAccess->getLesDevis($idDevis)[0];

    $lesDeviser = $devisAccess->getDeviser($idDevis);


    $pdf->newPage($leDevis,$leDevis->getAttribut('client'),$lesDeviser);
}

$pdf->displayPDF();

?>
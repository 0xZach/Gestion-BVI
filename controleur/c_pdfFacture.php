 <?php
/*
 * Description de c_pdfFacture.php
 *
 * auteur MASCLET Sylvain
 * Creation 10-02-2022
 * Derniere MAJ 08/03/2022
 * controleur de la page d'export pdf des factures
 */
if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}

require_once "$racine/modele/classes/PDF.php";
require_once "$racine/modele/DAO/Facture_DAO.php";
require_once "$racine/modele/DAO/Client_DAO.php";

$clientAccess = new Client_DAO();
$factureAccess = new Facture_DAO();
$pdf = new PDF();

if(isset($_GET['laFacture']))
    $idFacture = filter_var($_GET['laFacture'],FILTER_SANITIZE_STRING);


if(isset($_GET['export'])){
    
    foreach($_POST as $key => $value){
        if(strstr($key, 'FA')){
            if(isset($_GET['type'])){
                $lAvoir = $factureAccess->getLesAvoirs($key)[0];
                $laFacture = $lAvoir->getAttribut('facture');
                $pdf->newPage($lAvoir,$laFacture->getAttribut('client'),array());
            }
            else
            {
                $laFacture = $factureAccess->getLesFactures($key)[0];
                $lesFacturations = $factureAccess->getFacturations($key);
                $pdf->newPage($laFacture,$laFacture->getAttribut('client'),$lesFacturations);
            }
        }
    }
}
else
{
    if(isset($_GET['type'])){
        $lAvoir = $factureAccess->getLesAvoirs($idFacture)[0];
        $laFacture = $lAvoir->getAttribut('facture');
        $pdf->newPage($lAvoir,$laFacture->getAttribut('client'),array());
    }
    else
    {
        $laFacture = $factureAccess->getLesFactures($idFacture)[0];
        $lesFacturations = $factureAccess->getFacturations($idFacture);
        $pdf->newPage($laFacture,$laFacture->getAttribut('client'),$lesFacturations);
    }
}
    

$pdf->displayPDF();

?>
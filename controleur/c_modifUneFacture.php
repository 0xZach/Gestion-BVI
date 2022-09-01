<?php
/*
 * Description de c_modifUneFacture.php
 *
 * auteur MASCLET Sylvain
 * Creation 16-02-2022
 * Derniere MAJ 02/03/2022
 * controleur de la modification des Factures
 * 
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}




/* INCLUDES */

require_once "$racine/modele/DAO/Client_DAO.php";
require_once "$racine/modele/DAO/Produit_DAO.php";
require_once "$racine/modele/DAO/Facture_DAO.php";
require_once "$racine/modele/DAO/AuthUsr_DAO.php";




/* INITIALISATION DES VARIABLES */

$fichier= AuthUsr_DAO::isLoggedOn("vue/vue_modifUneFacture");

$clientAccess = new Client_DAO();
$produitAccess = new Produit_DAO();
$factureAccess = new Facture_DAO();

$lesClients = $clientAccess->getLesClients();
$clientSelect = '';

$tousLesProduits = $produitAccess->getProduits();
$lesIdsProduits= array();
$tableProduits = '';




/* RÉCUPÉRATION DE l'ID */

if(isset($_GET['goto']))
    $laFacture=$factureAccess->getLesFactures(filter_var($_GET['goto'],FILTER_SANITIZE_STRING))[0];


if(isset($_POST['idFacture']))
    $laFacture=$factureAccess->getLesFactures(filter_var($_POST['idFacture'],FILTER_SANITIZE_STRING))[0];


if(isset($_GET['id']))
    $laFacture=$factureAccess->getLesFactures(filter_var($_GET['id'],FILTER_SANITIZE_STRING))[0];


if(isset($_GET['ajoutLigne']))
    $laFacture=$factureAccess->getLesFactures(filter_var($_GET['ajoutLigne'],FILTER_SANITIZE_STRING))[0];





/* AJOUT D'UNE NOUVELLE LIGNE */

if(isset($_GET['ajoutLigne']))
    $factureAccess->ajoutFacturation($laFacture, $produitAccess->getProduits('PR00000'));






/* MODIFICATION DE LA FACTURE */

if(isset($_POST['confirmModif'])){
    
    
    
    /* MODIFICATION DE L'ÉTAT */
    
    $etatValid = filter_var($_POST['radioEtat'],FILTER_SANITIZE_STRING);
    if($laFacture->getAttribut('etat') !== $etatValid){
        $factureAccess->modifFacture($laFacture->getAttribut('id'), 'etat', $etatValid);
    }
    
    
    
    /* MODIFICATION DU CLIENT */
    
    $idClient = explode('@',filter_var($_POST['idClient'],FILTER_SANITIZE_STRING));
    if(count($idClient) > 1){
        if($idClient[1] !== $idClient[0])
            $factureAccess->modifFacture($laFacture->getAttribut('id'),'le_cli',$idClient[1]);
    }



    /* MODIFICATION DATE D'ÉCHÉANCE */
    
    $dateEcheance = filter_var($_POST['dateFin'],FILTER_SANITIZE_STRING);
    if($laFacture->getAttribut('echeance') !== $dateEcheance)
        $factureAccess->modifFacture($laFacture->getAttribut('id'),'dateEcheance',$dateEcheance);

    
    
    

    /* MODIFICATION DATE DE CRÉATION */
    
    $dateCreation = filter_var($_POST['dateCreation'],FILTER_SANITIZE_STRING);
    if($laFacture->getAttribut('creation') !== $dateCreation)
        $factureAccess->modifFacture($laFacture->getAttribut('id'),'dateCreation',$dateCreation);

    
    
    

    /* MODIFICATION MOYEN PAIEMENT */
    
    $moyen = filter_var($_POST['moyen_paiement'],FILTER_SANITIZE_STRING);
    if($laFacture->getAttribut('moyen') !== $moyen)
        $factureAccess->modifFacture($laFacture->getAttribut('id'),'moyen_paiement',$moyen);


    
    
    
    /* MODIFICATION DES PRODUITS  */
    
    foreach($_POST as $key => $value){
        
        // skey et svalue: s pour sanitized bien sur
        $skey = filter_var($key,FILTER_SANITIZE_STRING);
        $svalue = filter_var($value,FILTER_SANITIZE_STRING);
        
        
        if(strstr($skey, '@') !== false){
            $champs=explode('@',$skey);
            $laFacturation = $factureAccess->getFacturations($laFacture->getAttribut('id'),$champs[1])[0];
            
            switch($champs[0]){
                
                
                case 'qte':
                    if(intval($svalue) !== $laFacturation->getAttribut('quantite'))
                        $factureAccess->modifFacturation($laFacture->getAttribut('id'),$champs[1],'qte',$svalue,$laFacturation->getAttribut('ordre'));
                    break;
                    
                    
                    
                case 'remise':
                    if(floatval($svalue) !== $laFacturation->getAttribut('remise')){
                        $factureAccess->modifFacturation($laFacture->getAttribut('id'),$champs[1],'remise',$svalue,$laFacturation->getAttribut('ordre'));
                    }
                    break;
                    
                    
                    
                case 'nouvttc':
                    if($svalue !== ''){
                        $leProduit = $produitAccess->getProduits($champs[1])[0];
                        
                        
                        /*-CALCUL-DES-MONTANTS-ET-DE-LA-REMISE-*/
                        $mtnt_ht = $laFacturation->getAttribut('quantite')*$leProduit->getAttribut('ht');
                        $tva = $mtnt_ht*0.2;
                        $tva = number_format($tva,2,'.','');
                        $ttcMax = ($mtnt_ht+$tva);
                        $nouvRemise = number_format(100-($svalue/$ttcMax)*100,2,'.','');
                        /*------------------------------------*/
                        
                        
                        $factureAccess->modifFacturation($laFacture->getAttribut('id'),$champs[1],'remise',$nouvRemise,$laFacturation->getAttribut('ordre'));
                    }
                    break;
                    
                     
            }
        }

    }
    // on met à jour le montant hors taxe après modification
    $factureAccess->majMontantFacture($laFacture->getAttribut('id'));
}


/* MODIFICATION DE L'ID DU PRODUIT FACTURÉ */
    
if(isset($_GET['newProd'])){
    $lesIds=explode('@',filter_var($_GET['newProd'],FILTER_SANITIZE_STRING));
    if($lesIds[0] !== $lesIds[1])
        $factureAccess->modifFacturation(
                $laFacture->getAttribut('id'),
                $lesIds[0],
                'un_produit',
                $lesIds[1],
                intval($lesIds[2])
       );
    // on met à jour le montant hors taxe après modification
    $factureAccess->majMontantFacture($laFacture->getAttribut('id'));
}




/* SUPPRESSION D'UNE FACTURATION */

if (isset($_GET['prodtosuppr']) && isset($_GET['ordre'])){
    $factureAccess->supprProduitFacturation(
            $laFacture->getAttribut('id'),
            filter_var($_GET['prodtosuppr'],FILTER_SANITIZE_STRING),
            intval(filter_var($_GET['ordre'],FILTER_SANITIZE_STRING))
    );
}




/* RÉCUPÉRATION DE LA FACTURE APRES MODIFICATION */

if (isset($_POST['idFacture']))
    $laFacture = $factureAccess->getLesFactures(filter_var($_POST['idFacture'], FILTER_SANITIZE_STRING))[0];






/* SELECT DU CHOIX DU CLIENT */

$leClient = $laFacture->getAttribut('client');
// ancient id @ nouvel id
$clientSelect .= '<input form="modifFact" type="hidden" name="idClient" value="'.$leClient->getAttribut('id').'@">';
$clientSelect .= '<select class="selectFacture" name="nomClient">';
$clientSelect .= '<option id="'.$leClient->getAttribut('id').'">'.$leClient->getAttribut('nom').'</option>';
for($i=0;$i<count($lesClients);$i++){
    if($lesClients[$i]->getAttribut('id') !== $leClient->getAttribut('id'))
        $clientSelect .= '<option id="'.$lesClients[$i]->getAttribut('id').'">'.$lesClients[$i]->getAttribut('nom').'</option>';
}
$clientSelect .= '</select>';





/* TD DES PRODUITS */

$lesFacturations = $factureAccess->getFacturations($laFacture->getAttribut('id'));
$lesProduitsNonFacturer = $factureAccess->getProduitsNonFacturer($laFacture->getAttribut('id'));

for($i=0;$i<count($lesFacturations);$i++){
    
    
    $leProduit = $lesFacturations[$i]->getAttribut('produit');
    
    
    /*-CALCUL-DES-MONTANTS-ET-DE-LA-REMISE-*/
    $mtnt_ht = $lesFacturations[$i]->getAttribut('quantite')*$leProduit->getAttribut('ht');
    $tva = $mtnt_ht*0.2;
    $tva = number_format($tva,2,'.','');
    $remise= $lesFacturations[$i]->getAttribut('remise');
    $ttcMax=$mtnt_ht+$tva;
    $ttc = $ttcMax-$ttcMax*($remise/100);
    $ttc = number_format($ttc,2,'.','');
    /*------------------------------------*/
    
    
    $tableProduits .= '<tr>';
    
    
    
    /* CAS PRODUIT PR00000 */
    
    if($leProduit->getAttribut('id') === 'PR00000'){
        
        $tableProduits .= '<td colspan=7>';
        
        // facture en cours
        if($laFacture->getAttribut('etat') === 'FE'){
            $tableProduits .= '<input type="hidden" name="id@'.$leProduit->getAttribut('id').'" value="">';
            $tableProduits .= '<select class="selectFacture" name="nomProduit@'.$lesFacturations[$i]->getAttribut('ordre').'">';
            $tableProduits .= '<option id="'.$leProduit->getAttribut('id').'">'.$leProduit->getAttribut('libelle').'</option>';
            for($j=0;$j<count($lesProduitsNonFacturer);$j++){
                if($lesProduitsNonFacturer[$j]->getAttribut('id') !== 'PR00000')
                    $tableProduits .= '<option id="'.$lesProduitsNonFacturer[$j]->getAttribut('id').'">'.$lesProduitsNonFacturer[$j]->getAttribut('libelle').'</option>';
            }
            $tableProduits .= '</select>';
        }
        
        $tableProduits .= '</td>';
        //$tableProduits .= '<td colspan=6></td>';
    }
    
    
    
    /* CAS PRODUIT NORMAL + FACTURE EN COURS */
    
    if($leProduit->getAttribut('id') !== 'PR00000' && $laFacture->getAttribut('etat') === 'FE'){
        
        
        /* SELECT */
        
        $tableProduits .= '<td>';
        // name = ancienID value= nouvelID
        $tableProduits .= '<input type="hidden" name="id@'.$leProduit->getAttribut('id').'" value="">';  
        $tableProduits .= '<select class="selectFacture" name="nomProduit@'.$lesFacturations[$i]->getAttribut('ordre').'">';
        
        // le produit PR00000 qui sert d'espace
        $tableProduits .= '<option id="PR00000">-- [Option vide] --</option>';
        
        // le produit courant
        $tableProduits .= '<option id="'.$leProduit->getAttribut('id').'" selected >'.$leProduit->getAttribut('libelle').'</option>';
        
        // les produits non facturés
        for($j=0;$j<count($lesProduitsNonFacturer);$j++){
            if($lesProduitsNonFacturer[$j]->getAttribut('id') !== 'PR00000')
                $tableProduits .= '<option id="'.$lesProduitsNonFacturer[$j]->getAttribut('id').'">'.$lesProduitsNonFacturer[$j]->getAttribut('libelle').'</option>';
        }
        $tableProduits .= '</select></td>';
        
        
        
        
        /* INFOS */
        
        $tableProduits .= '<td><input form="modifFact" class="infos" name="qte@'.$leProduit->getAttribut('id').'" type="number" min="0" value="'.$lesFacturations[$i]->getAttribut('quantite').'"></td>';
        $tableProduits .= '<td><p>'.$leProduit->getAttribut('ht').'<p></td>';
        $tableProduits .= '<td><p>'.$mtnt_ht.'</p></td>';
        $tableProduits .= '<td><p>'.$tva.'</p></td>';
        $tableProduits .= '<td>'
                        . '<input form="modifFact" class="infos" name="remise@'.$leProduit->getAttribut('id').'" '
                        . 'type="number" min="0" max="100" step="0.01" value="'.$remise.'">'
                        . '</td>';
        $tableProduits .= '<td>'
                        . '<input form="modifFact" type="hidden" name="nouvttc@'.$leProduit->getAttribut('id').'" type="number" min="0" step="0.01" value="">'
                        . '<input class="infos ttc" name="ttc@'.$leProduit->getAttribut('id').'" type="number" min="0" step="0.01" value="'.$ttc.'">'
                        . '</td>';
    
    }
    

    /* CAS PRODUIT NORMAL + FACTURE VALIDÉE */
    
    if($leProduit->getAttribut('id') !== 'PR00000' && $laFacture->getAttribut('etat') === 'FV'){
        $tableProduits .= '<td>';
        $tableProduits .= '<p>'.$leProduit->getAttribut('libelle').'</p>';
        $tableProduits .= '</td>';


        $tableProduits .= '<td><p>'.$lesFacturations[$i]->getAttribut('quantite').'</p></td>';
        $tableProduits .= '<td><p>'.$leProduit->getAttribut('ht').'<p></td>';
        $tableProduits .= '<td><p>'.$mtnt_ht.'</p></td>';
        $tableProduits .= '<td><p>'.$tva.'</p></td>';
        $tableProduits .= '<td><p>'.$remise.'</p></td>';
        $tableProduits .= '<td><p>'.$ttc.'</p></td>';
    }    
    
    if($laFacture->getAttribut('etat') === 'FE'){
        // bouton de suppression d'un produit facturé
        $tableProduits .= '<td>';
        $tableProduits .= '<a '
                        . 'href="?action=lafacture&'
                        . 'prodtosuppr='.$leProduit->getAttribut('id').'&'
                        . 'id='.$laFacture->getAttribut('id').'&'
                        . 'ordre='.$lesFacturations[$i]->getAttribut('ordre').'">'
                        . '<i class="fa-solid fa-minus"></i>'
                        . '</a>';
        $tableProduits .= '</td>';
    }
    
    $tableProduits .= '</tr>';
}



/* BOUTON DE SUPPRESSION D'UNE FACTURE */

$buttonSupprFacture = '<a class="btn btn_nav suppr" href="?action=lesfactures&tosuppr='.$laFacture->getAttribut('id').'">'
                   . '<i class="fa-solid fa-trash"></i>'
                   . '</a>';





$titre="Modification d'une Facture";
include_once "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
?>
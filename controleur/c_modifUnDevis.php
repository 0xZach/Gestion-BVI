<?php
/*
 * Description de c_modifUnDevis.php
 *
 * auteur MASCLET Sylvain
 * Creation 18-02-2022
 * Derniere MAJ 02/03/2022
 * controleur de la modification d'un Devis
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

$fichier= AuthUsr_DAO::isLoggedOn("vue/vue_modifUnDevis");

$clientAccess = new Client_DAO();
$produitAccess = new Produit_DAO();
$devisAccess = new Devis_DAO();

$lesClients = $clientAccess->getLesClients();
$clientSelect = '';

$tousLesProduits = $produitAccess->getProduits();
$lesIdsProduits= array();
$tableProduits = '';





/* RÉCUPÉRATION DE l'ID */

if(isset($_GET['goto']))
    $leDevis=$devisAccess->getLesDevis(filter_var($_GET['goto'],FILTER_SANITIZE_STRING))[0];


if(isset($_POST['idDevis']))
    $leDevis=$devisAccess->getLesDevis(filter_var($_POST['idDevis'],FILTER_SANITIZE_STRING))[0];


if(isset($_GET['id']))
    $leDevis=$devisAccess->getLesDevis(filter_var($_GET['id'],FILTER_SANITIZE_STRING))[0];


if(isset($_GET['ajoutLigne']))
    $leDevis=$devisAccess->getLesDevis(filter_var($_GET['ajoutLigne'],FILTER_SANITIZE_STRING))[0];





/* AJOUT D'UNE NOUVELLE LIGNE */

if(isset($_GET['ajoutLigne']))
    $devisAccess->ajoutDeviser($leDevis, $produitAccess->getProduits('PR00000'));






/* MODIFICATION DU DEVIS */

if(isset($_POST['confirmModif'])){
    
    
    
    /* MODIFICATION DE L'ÉTAT */
    
    $etatValid = filter_var($_POST['radioEtat'],FILTER_SANITIZE_STRING);
    if($leDevis->getAttribut('etat') !== $etatValid){
        $devisAccess->modifDevis($leDevis->getAttribut('id'), 'etat', $etatValid);
    }
    

    
    
    /* MODIFICATION DU CLIENT */
    
    $idClient = explode('@',filter_var($_POST['idClient'],FILTER_SANITIZE_STRING));
    if(count($idClient) > 1){
        if($idClient[1] !== $idClient[0])
            $devisAccess->modifDevis($leDevis->getAttribut('id'),'le_cli',$idClient[1]);
    }

    


    /* MODIFICATION DATE DE FIN DE VALIDITÉ */
    
    $dateFin = filter_var($_POST['dateFin'],FILTER_SANITIZE_STRING);
    if($leDevis->getAttribut('fin') !== $dateFin)
        $devisAccess->modifDevis($leDevis->getAttribut('id'),'date_valid_fin',$dateFin);


    
    
    
    /* MODIFICATION DATE DE CRÉATION */
    
    $dateCreation = filter_var($_POST['dateCreation'],FILTER_SANITIZE_STRING);
    if($leDevis->getAttribut('creation') !== $dateCreation)
        $devisAccess->modifDevis($leDevis->getAttribut('id'),'dateCreation',$dateCreation);


    
    
    /* MODIFICATION DES PRODUITS  */
    
    foreach($_POST as $key => $value){
        

        // skey et svalue: s pour sanitized bien sur
        $skey = filter_var($key,FILTER_SANITIZE_STRING);
        $svalue = filter_var($value,FILTER_SANITIZE_STRING);
        
        
        if(strstr($skey, '@') !== false){
            
            // on décompose la clef en un tableau sous la forme [(champ,valeur)]
            $champs=explode('@',$skey);
            
            // on récupère les liens entre produits et devis
            $leDeviser = $devisAccess->getDeviser($leDevis->getAttribut('id'),$champs[1])[0];
             
            
            
            switch($champs[0]){
                
                
                
                case 'qte':
                    if(intval($svalue) !== $leDeviser->getAttribut('quantite'))
                        $devisAccess->modifDeviser($leDevis->getAttribut('id'),$champs[1],'qte',$svalue,$leDeviser->getAttribut('ordre'));
                    break;
                    
                    
                    
                case 'remise':
                    if(floatval($svalue) !== $leDeviser->getAttribut('remise')){
                        $devisAccess->modifDeviser($leDevis->getAttribut('id'),$champs[1],'remise',$svalue,$leDeviser->getAttribut('ordre'));
                    }
                    break;
                
                    
                    
                case 'nouvttc':
                    if($svalue !== ''){
                        $leProduit = $produitAccess->getProduits($champs[1])[0];
                        
                        
                        /*-CALCUL-DES-MONTANTS-ET-DE-LA-REMISE-*/
                        $mtnt_ht = $leDeviser->getAttribut('quantite')*$leProduit->getAttribut('ht');
                        $tva = $mtnt_ht*0.2;
                        $tva = number_format($tva,2,'.','');
                        $ttcMax = ($mtnt_ht+$tva);
                        $nouvRemise = number_format(100-($svalue/$ttcMax)*100,2,'.','');
                        /*------------------------------------*/
                        
                        
                        $devisAccess->modifDeviser($leDevis->getAttribut('id'),$champs[1],'remise',$nouvRemise,$leDeviser->getAttribut('ordre'));
                        $devisAccess->modifDevis($leDevis->getAttribut('id'),'net_apayer',$svalue);
                    }
                    break;
                    
                
            }
        }

    }   

}



/* MODIFICATION DE L'ID DU PRODUIT FACTURÉ */

if(isset($_GET['newProd'])){
    $lesIds=explode('@',filter_var($_GET['newProd'],FILTER_SANITIZE_STRING));
    if($lesIds[0] !== $lesIds[1])
        $devisAccess->modifDeviser(
                $leDevis->getAttribut('id'),
                $lesIds[0],
                'un_produit',
                $lesIds[1],
                intval($lesIds[2])
       );
}




/* SUPPRESSION D'UN DEVIS */

if (isset($_GET['prodtosuppr']) && isset($_GET['ordre'])){
    $devisAccess->supprProduitDeviser(
            $leDevis->getAttribut('id'),
            filter_var($_GET['prodtosuppr'],FILTER_SANITIZE_STRING),
            intval(filter_var($_GET['ordre'],FILTER_SANITIZE_STRING))
    );
}




/* RÉCUPÉRATION DU DEVIS APRES MODIFICATION */
if (isset($_POST['idDevis']))
    $leDevis = $devisAccess->getLesDevis(filter_var($_POST['idDevis'], FILTER_SANITIZE_STRING))[0];





/* SELECT DU CHOIX DU CLIENT */

$leClient = $leDevis->getAttribut('client');
$clientSelect .= '<input form="modifDevis" type="hidden" name="idClient" value="'.$leClient->getAttribut('id').'@">'; // ancient id @ nouvel id
$clientSelect .= '<select class="lesSelects" name="nomClient">';
$clientSelect .= '<option id="'.$leClient->getAttribut('id').'">'.$leClient->getAttribut('nom').'</option>';
for($i=0;$i<count($lesClients);$i++){
    if($lesClients[$i]->getAttribut('id') !== $leClient->getAttribut('id'))
        $clientSelect .= '<option id="'.$lesClients[$i]->getAttribut('id').'">'.$lesClients[$i]->getAttribut('nom').'</option>';
}
$clientSelect .= '</select>';





/* TD DES PRODUITS */

$lesDeviser = $devisAccess->getDeviser($leDevis->getAttribut('id'));
$lesProduitsNonFacturer = $devisAccess->getProduitsNonFacturer($leDevis->getAttribut('id'));


for($i=0;$i<count($lesDeviser);$i++){
    
    
    $leProduit = $lesDeviser[$i]->getAttribut('produit');
    
    
    /*-CALCUL-DES-MONTANTS-ET-DE-LA-REMISE-*/
    $mtnt_ht = $lesDeviser[$i]->getAttribut('quantite')*$leProduit->getAttribut('ht');
    $tva = $mtnt_ht*0.2;
    $tva = number_format($tva,2,'.','');
    $remise= $lesDeviser[$i]->getAttribut('remise');
    $ttcMax=$mtnt_ht+$tva;
    $ttc = $ttcMax-$ttcMax*($remise/100);
    $ttc = number_format($ttc,2,'.','');
    /*------------------------------------*/
    
    
    $tableProduits .= '<tr>';
    
    
    // cas produit espace
    if($leProduit->getAttribut('id') === 'PR00000'){
        
        $tableProduits .= '<td colspan=7>';
        
        // facture en cours
        if($leDevis->getAttribut('etat') === 'DE'){
            $tableProduits .= '<input type="hidden" name="id@'.$leProduit->getAttribut('id').'" value="">';
            $tableProduits .= '<select class="lesSelects selectDevis" name="nomProduit@'.$lesDeviser[$i]->getAttribut('ordre').'">';
            $tableProduits .= '<option id="'.$leProduit->getAttribut('id').'">'.$leProduit->getAttribut('libelle').'</option>';
            for($j=0;$j<count($lesProduitsNonFacturer);$j++){
                if($lesProduitsNonFacturer[$j] !== $lesDeviser[$i]->getAttribut('facture'))
                    $tableProduits .= '<option id="'.$lesProduitsNonFacturer[$j]->getAttribut('id').'">'.$lesProduitsNonFacturer[$j]->getAttribut('libelle').'</option>';
            }
            $tableProduits .= '</select>';
        }
        
        $tableProduits .= '</td>';
        //$tableProduits .= '<td colspan=6></td>';
    }
    
    
    
    
    // cas produit normal + facture en cours
    if($leProduit->getAttribut('id') !== 'PR00000' && $leDevis->getAttribut('etat') === 'DE'){
        
        
        /* SELECT */
        
        $tableProduits .= '<td>';
        // name = ancienID value= nouvelID
        $tableProduits .= '<input type="hidden" name="id@'.$leProduit->getAttribut('id').'" value="">';  
        $tableProduits .= '<select class="lesSelects selectDevis" name="nomProduit@'.$lesDeviser[$i]->getAttribut('ordre').'">';
        
        // le produit PR00000 qui sert d'espace
        $tableProduits .= '<option id="PR00000">-- [Option vide] --</option>';
        
        // le produit courant
        $tableProduits .= '<option id="'.$leProduit->getAttribut('id').'" selected >'.$leProduit->getAttribut('libelle').'</option>';
        
        // les produits non devisés
        for($j=0;$j<count($lesProduitsNonFacturer);$j++){
            if($lesProduitsNonFacturer[$j]->getAttribut('id') !== 'PR00000')
                $tableProduits .= '<option id="'.$lesProduitsNonFacturer[$j]->getAttribut('id').'">'.$lesProduitsNonFacturer[$j]->getAttribut('libelle').'</option>';
        }
        $tableProduits .= '</select></td>';
        
        
        
        /* INFOS */
        
        $tableProduits .= '<td><input form="modifDevis" class="infos" name="qte@'.$leProduit->getAttribut('id').'" type="number" min="0" value="'.$lesDeviser[$i]->getAttribut('quantite').'"></td>';
        $tableProduits .= '<td><p>'.$leProduit->getAttribut('ht').'<p></td>';
        $tableProduits .= '<td><p>'.$mtnt_ht.'</p></td>';
        $tableProduits .= '<td><p>'.$tva.'</p></td>';
        $tableProduits .= '<td>'
                        . '<input form="modifDevis" class="infos" name="remise@'.$leProduit->getAttribut('id').'" '
                        . 'type="number" min="0" max="100" step="0.01" value="'.$remise.'">'
                        . '</td>';
        $tableProduits .= '<td>'
                        . '<input form="modifDevis" type="hidden" name="nouvttc@'.$leProduit->getAttribut('id').'" type="number" min="0" step="0.01" value="">'
                        . '<input form="modifDevis" class="infos ttc" name="ttc@'.$leProduit->getAttribut('id').'" type="number" min="0" step="0.01" value="'.$ttc.'">'
                        . '</td>';
    
    }
    
    
    
    // cas produit normal + facture validée
    if($leProduit->getAttribut('id') !== 'PR00000' && $leDevis->getAttribut('etat') === 'DV'){
        $tableProduits .= '<td>';
        $tableProduits .= '<p>'.$leProduit->getAttribut('libelle').'</p>';
        $tableProduits .= '</td>';


        $tableProduits .= '<td><p>'.$lesDeviser[$i]->getAttribut('quantite').'</p></td>';
        $tableProduits .= '<td><p>'.$leProduit->getAttribut('ht').'<p></td>';
        $tableProduits .= '<td><p>'.$mtnt_ht.'</p></td>';
        $tableProduits .= '<td><p>'.$tva.'</p></td>';
        $tableProduits .= '<td><p>'.$remise.'</p></td>';
        $tableProduits .= '<td><p>'.$ttc.'</p></td>';
    }    
    
    if($leDevis->getAttribut('etat') === 'DE'){
        // bouton de suppression d'un produit facturé
        $tableProduits .= '<td>';
        $tableProduits .= '<a '
                        . 'href="?action=ledevis&'
                        . 'prodtosuppr='.$leProduit->getAttribut('id').'&'
                        . 'id='.$leDevis->getAttribut('id').'&'
                        . 'ordre='.$lesDeviser[$i]->getAttribut('ordre').'">'
                        . '<i class="fa-solid fa-minus"></i>'
                        . '</a>';
        $tableProduits .= '</td>';
    }
    
    $tableProduits .= '</tr>';
    
}



/* BOUTON DE SUPPRESSION D'UN DEVIS */

$boutonSuppression = '<a class="btn btn_nav suppr" href="?action=lesdeviss&tosuppr='.$leDevis->getAttribut('id').'">'
                   . '<i class="fa-solid fa-trash"></i>'
                   . '</a>';




$titre="Modification d'un Devis";
include_once "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
?>
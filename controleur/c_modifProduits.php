<?php
/*
 * Description de c_modifProduits.php
 *
 * auteur MASCLET Sylvain
 * Creation 07-02-2022
 * Derniere MAJ 07/03/2022
 * controleur de l'affichage et de modification des produits
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}



/* INCLUDES */

require_once "$racine/modele/DAO/Produit_DAO.php";
require_once "$racine/modele/DAO/AuthUsr_DAO.php";




/* INITIALISATION DES VARIABLES */

$fichier = AuthUsr_DAO::isLoggedOn("vue/vue_modifProduits");
$prodAccess = new Produit_DAO();
$lesProduits = array();
$champsProduits=array('libelle','pu_ht','type_produit','tx_horaire','code_barre');
$tabRows="";




/* SUPPRESSION D'UN PRODUIT */

if(isset($_GET['tosuppr'])){
    $prodAccess->supprProduit(filter_var($_GET['tosuppr']));
    
    // on re récupère les produits après la suppression
    $lesProduits = $prodAccess->getProduits();
}





/* MODIFICATION D'UN PRODUIT */

if(isset($_GET['id']) && isset($_GET['value'])){
    
    // on découpe le string id 
    // car il est composé du nom de champ d'un produit de son id et de son type (bien ou service)
    $lesValeurs= explode('@',filter_var($_GET['id'],FILTER_SANITIZE_STRING));
    if($lesValeurs[0] !== "pu_ht")
        $nouvValeur = filter_var($_GET['value'],FILTER_SANITIZE_STRING);
    else
        $nouvValeur = floatval(filter_var($_GET['value'],FILTER_SANITIZE_STRING))/1.2;
    
    // modification du produit
    $prodAccess->modifProduit($lesValeurs[1], $lesValeurs[0], $nouvValeur);
    
    // on re récupère les produits après la modification
    $lesProduits = $prodAccess->getProduits();
      
}




/* RECHERCHE D'UN PRODUIT */

// si on est en mode recherche, on n'affiche que les produits liés aux valeurs recherchées
if(isset($_POST['recherche'])){
    $paramRecherche= filter_var($_POST['recherche'], FILTER_SANITIZE_STRING);
    $lesProduits = $prodAccess->getProduits($paramRecherche);
}




/* REMPLISSAGE DE LA VARIABLE D'AFFICHAGE DES PRODUITS */

if($lesProduits !== array()){

    for($i=0;$i<sizeof($lesProduits);$i++){
        
        if($lesProduits[$i]->getAttribut('id') !== 'PR00000'){
            $tabRows.="<tr>";

            for($j=0;$j<sizeof($champsProduits);$j++){
                /*
                 * explication du fonctionnement:
                 * à chaque tour on va créer un <td> avec à l'intérieur un input;
                 * cet input prend en id un collage entre le nom du champ et un @ comme délimiteur;
                 * example:
                 * nom@PR00001
                 * 
                 * ensuite la valeur affichée sera la valeur prise depuis la bd;
                 * 
                 */
                $tabRows.='<td colspan="2">';

                $idDuProduit=$champsProduits[$j].'@';


                switch($champsProduits[$j]){
                    
                    
                    case 'code_barre':
                        if($lesProduits[$i] instanceof Bien){
                            $tabRows.='<input class="infos" id="'.$idDuProduit.$lesProduits[$i]->getAttribut('id').'" type="text" size="10" value="';

                            // switch pour vérifier la valeur à afficher
                            $tabRows .= $lesProduits[$i]->getCodeBarre();
                            $tabRows .= '">';
                        }
                        else
                            $tabRows.='<p></p>';
                        break;
                    
                    
                    case 'type_produit':
                        if($lesProduits[$i] instanceof Bien)
                            $tabRows .= '<p>Bien</p>';
                        else
                            $tabRows .= '<p>Service</p>';
                        break;
                    
                        
                    case 'tx_horaire':
                        if($lesProduits[$i] instanceof Service && $lesProduits[$i]->getTaux())
                            $tabRows .= '<i id="checked" class="fa-solid fa-check"></i>';
                        else
                            $tabRows .= '<i id="unchecked"></i>';
                        break;
                    
                    case 'pu_ht':
                        $tabRows.='<input class="infos" id="'.$idDuProduit.$lesProduits[$i]->getAttribut('id').'" type="text" size="10" value="';

                        // switch pour vérifier la valeur à afficher
                        $tabRows .= $lesProduits[$i]->getAttribut("ttc");
                        $tabRows .= '">';
                        break;
                        
                    default:
                        $tabRows.='<input class="infos" id="'.$idDuProduit.$lesProduits[$i]->getAttribut('id').'" type="text" size="10" value="';

                        // switch pour vérifier la valeur à afficher
                        $tabRows .= $lesProduits[$i]->getAttribut($champsProduits[$j]);
                        $tabRows .= '">';
                        break;
                        
                }
                $tabRows .= '</td>';

            }
            // bouton de suppression avec une requête get renvoyant l'id du produit
            $tabRows .= '<td>'
                        . '<a class="btn btn_nav suppr" href="?action=lesproduits&tosuppr='.$lesProduits[$i]->getAttribut('id').'">'
                            . '<i class="fa-solid fa-trash"></i>'
                        . '</a>'
                      . '</td>';
            $tabRows .= '</tr>';
        }
    }
}





$titre="Liste des Produits";
include "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
?>
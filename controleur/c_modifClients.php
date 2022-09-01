<?php
/*
 * Description de c_modifClients.php
 *
 * auteur MASCLET Sylvain
 * Creation 31-01-2022
 * Derniere MAJ 28/02/2022
 * controleur de l'affichage et de modification des clients
 */

if ( $_SERVER["SCRIPT_FILENAME"] == __FILE__ ){
    $racine="..";
}



/* INCLUDES */

require_once "$racine/modele/DAO/Client_DAO.php";
require_once "$racine/modele/DAO/AuthUsr_DAO.php";



/* INITIALISATION DES VARIABLES */


$fichier = AuthUsr_DAO::isLoggedOn('vue/vue_modifClients');
$clientAccess = new Client_DAO();
$lesClients = array();
$champsClients=array('nom','ville','codepostal','adresse','tel_fix','tel_mobile','mel');
$tabRows='';




/* SUPPRESSION D'UN CLIENT */

if(isset($_GET['tosuppr'])){
    // on supprime le client avec l'id désiré
    $clientAccess->supprClient(filter_var($_GET['tosuppr'],FILTER_SANITIZE_STRING));
    
    // on re récupère les clients après la modification
    $lesClients = $clientAccess->getLesClients();
}




/* MODIFICATION D'UN CLIENT */

if(isset($_GET['id']) && isset($_GET['value'])){
    
    // on découpe le string id 
    // car il est composé du nom de champ d'un client et de son id
    $lesValeurs= explode('@',filter_var($_GET['id'],FILTER_SANITIZE_STRING));
    
    // si on a le champ modifié est le tel mobile ou fix, alors on enlève les
    // possibles espaces entre les nombres
    if($lesValeurs[0] === "tel_mobile" || $lesValeurs[0] === "tel_fix"){
        $nouvValeur = str_replace(' ','',filter_var($_GET['value'],FILTER_SANITIZE_STRING));
    }
    else
    {
        $nouvValeur = filter_var($_GET['value'],FILTER_SANITIZE_STRING);
    }
    
    // on appel la fonction qui va modifier dans la bd les infos du client
    $clientAccess->modifClient($lesValeurs[1],$lesValeurs[0],$nouvValeur);   
    
    // on re récupère les clients après la modification
    $lesClients = $clientAccess->getLesClients();
}




/* RECHERCHE DES CLIENTS */

// on n'affiche que les clients liés aux valeurs recherchées
if(isset($_POST['recherche'])){
    $paramRecherche= str_replace(' ','',filter_var($_POST['recherche'], FILTER_SANITIZE_STRING));
    $lesClients = $clientAccess->getLesClients($paramRecherche);
}





/* AFFICHAGE DES CLIENTS */

if($lesClients !== array()){

    for($i=0;$i<sizeof($lesClients);$i++){
        $tabRows.='<tr>';

        for($j=0;$j<sizeof($champsClients);$j++){
            /*
             * explication du fonctionnement:
             * à chaque tour on va créer un <td> avec à l'intérieur un input;
             * cet input prend en id un collage entre le nom du champ, un @ comme délimiteur et l'id du client;
             * example:
             * nom@CL00001
             * 
             * ensuite la valeur affichée sera soit juste la valeur prise depuis la bd
             * soit, dans le cas des numéro de téléphone, la valeur, à laquelle on ajoutera artificicellement
             * des espaces pour le rendu visuel;
             *
            */
            $tabRows.='<td colspan="2"';

            $tabRows.='><input class="infos" id="'.$champsClients[$j].'@'.$lesClients[$i]->getAttribut('id').'" type="text" value="';
            if($champsClients[$j]==="tel_fix" || $champsClients[$j]==="tel_mobile"){
                for($k=0;$k<strlen($lesClients[$i]->getAttribut($champsClients[$j]));$k++){
                    if($k%2!==0 || $k===0)
                        $tabRows .= $lesClients[$i]->getAttribut($champsClients[$j])[$k];
                    else
                        $tabRows .= " ".$lesClients[$i]->getAttribut($champsClients[$j])[$k];
                }
            }
            else
            {
                $tabRows .= $lesClients[$i]->getAttribut($champsClients[$j]);
            }
            $tabRows .= '"></td>';
        }
        $tabRows .= '<td><a class="btn btn_nav suppr" href="?action=lesclients&tosuppr='.$lesClients[$i]->getAttribut('id').'"><i class="fa-solid fa-trash"></i></a></td>';
        $tabRows .= '</tr>';
    }
}


$titre="Liste des Clients";
include "$racine/vue/entete.html.php";
if(AuthUsr_DAO::isLoggedOn('') === '')
    include "$racine/vue/liste.html.php";
include "$racine/$fichier.php";
include "$racine/vue/pied.html.php";
?>
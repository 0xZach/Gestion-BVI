<?php
/*
 * Description de c_principal.php
 *
 * auteur MASCLET Sylvain
 * Creation 26-01-2022
 * Derniere MAJ 11/02/2022
 * controleur renvoyant vers les autres controleurs
 */

require_once "$racine/modele/DAO/AuthUsr_DAO.php";



function controleurPrincipal(string $action){

    switch($action){
        
        case "accueil":
            return "c_accueil";
            
        case "ajoutclient":
            return "c_ajoutClient";
            
        case "ajoutproduit":
            return "c_ajoutProduit";
            
        case "ajoutdevis":
            return "c_ajoutDevis";
            
        case "ajoutfacture":
            return "c_ajoutFacture";
            
        case "lesclients":
            return "c_modifClients";
            
        case "lesproduits":
            return "c_modifProduits";
            
        case "lesdevis":
            return "c_listeDevis";
            
        case "lesfactures":
            return "c_listeFactures";
            
        case "ledevis":
            return "c_modifUnDevis";
            
        case "lafacture":
            return "c_modifUneFacture";
            
        case "pdffacture":
            return "c_pdfFacture";
            
        case "pdfdevis":
            return "c_pdfDevis";
        
        case 'pdfecheances':
            return "c_pdfEcheancier";
        
        case "lesavoirs":
            return "c_listeAvoirs";
        
        case "lesecheances":
            return "c_listeEcheances";
        
        case 'lesreglements':
            return "c_listeReglements";
        
        case "off":
            AuthUsr_DAO::logout();
        default:
            return "c_auth";
    }
    
}

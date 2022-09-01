<?php
/*
 * Description de Devis.php
 *
 * auteur MASCLET Sylvain
 * Creation 11-02-2022
 * Derniere MAJ 14/02/2022
 * Classe décrivant un Devis
 * 
 */

require_once "$racine/modele/classes/Client.php";


class Devis {
    // Attributs
    private $id_devis;
    private $dateCreation;
    private $date_valid_fin;
    private $net_apayer;
    private $etat;
    private $le_cli;
    private $lesProduits;
    
    // Constructeur
    public function __construct(string $p_id, string $p_dateCreation, string $p_dateFinValid, string $p_netapayer, string $p_etat) {
        $this->id_devis=$p_id;
        $this->dateCreation=$p_dateCreation;
        $this->date_valid_fin=$p_dateFinValid;
        $this->net_apayer=$p_netapayer;
        $this->etat=$p_etat;
    }
    
    
    // Gets & Sets
    public function getId(){
        return $this->id_devis;
    }
    
    public function getDateCrea(){
        return $this->dateCreation;
    }
    
    public function getDateFinValid(){
        return $this->date_valid_fin;
    }
    
    public function getNetApayer(){
        return $this->net_apayer;
    }
    
    public function getEtat(){
        return $this->etat;
    }
    
    public function getClient(){
        return $this->le_cli;
    }
    
    public function getLesProduits(){
        return $this->lesProduits;
    }
    
    
    
    //------------------------------------//
    

    public function setId(string $p){
        $this->id_devis=$p;
    }
    
    public function setDateCrea(string $p){
        $this->dateCreation=$p;
    }
    
    public function setDateFinValid(string $p){
        $this->date_valid_fin=$p;
    }
    
    public function setNetApayer(string $p){
        $this->net_apayer=$p;
    }
    
    public function setEtat(string $p){
        $this->etat=$p;
    }
    
    public function setClient(Client $p){
        $this->le_cli=$p;
    }
    
    public function setLesProduits(array $p){
        $this->lesProduits=$p;
    }
    
    
    // fonctions
    public function getAttribut(string $attribut)
    {
        switch ($attribut){
            case 'id':
                return $this->id_devis;
                break;
            case 'creation':
                return $this->dateCreation;
                break;
            case 'fin':
                return $this->date_valid_fin;
                break;
            case 'etat':
                return $this->etat;
                break;
            case 'net_apayer':
                return $this->net_apayer;
                break;
            case 'client':
                return $this->le_cli;
                break;
            case 'produits':
                return $this->lesProduits;
                break;
            default:
                return '';
                break;
        }
    }
    
}

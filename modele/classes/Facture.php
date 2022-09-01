<?php
/*
 * Description de Facture.php
 *
 * auteur MASCLET Sylvain
 * Creation 11-02-2022
 * Derniere MAJ 14/02/2022
 * Classe décrivant une Facture
 * 
 */

class Facture {
    // Attributs
    private $id_Facture;
    private $dateCreation;
    private $dateEcheance;
    private $ht;
    private $accompte;
    private $etat;
    private $moyen_paiement;
    private $le_cli;
    private $lesProduits;
    
    // Constructeur
    public function __construct(
            string $p_id, 
            string $p_dateCreation, 
            float $p_ht, 
            float $p_accompte, 
            string $p_etat, 
            string $p_moyen, 
            string $p_dateEcheance=''
            ) {
        $this->id_Facture=$p_id;
        $this->dateCreation=$p_dateCreation;
        $this->ht=$p_ht;
        $this->accompte=$p_accompte;
        $this->etat=$p_etat;
        $this->moyen_paiement=$p_moyen;
        $this->le_cli = null;
        $this->lesProduits = array();
        $this->dateEcheance=$p_dateEcheance;
    }
    
    
    // Gets & Sets
    public function getId(){
        return $this->id_Facture;
    }
    
    public function getDateCrea(){
        return $this->dateCreation;
    }
    
    public function getDateEche(){
        return $this->dateEcheance;
    }
    
    public function getMontants(){
        $tva = $this->ht*0.2;
        return array('ht'=>$this->ht,'tva'=> $tva,'ttc'=> $this->ht+$tva);
    }
    
    public function getAccompte(){
        return $this->accompte;
    }
    
    public function getEtat(){
        return $this->etat;
    }
    
    public function getMoyenPaiement(){
        return $this->moyen_paiement;
    }
    
    public function getClient(){
        return $this->le_cli;
    }
    
    public function getLesProduits(){
        return $this->lesProduits;
    }
    
    
    
    //------------------------------------//
    

    public function setId(string $p){
        $this->id_Facture = $p;
    }
    
    public function setDateCrea(string $p){
        $this->dateCreation=$p;
    }
    
    public function setDateEche(string $p){
        $this->dateEcheance=$p;
    }
    
    public function setHT(float $p){
        $this->ht=$p;
    }
    
    public function setAccompt(float $p){
        $this->accompte=$p;
    }
    
    public function setEtat(string $p){
        $this->etat=$p;
    }
    
    public function setMoyenPaiement(string $p){
        $this->moyen_paiement=$p;
    }
    
    public function setClient(Client $p){
        $this->le_cli = $p;
    }
    
    public function setLesProduits(array $p){
        $this->lesProduits=$p;
    }
    
    
    
    // fonctions
    public function getAttribut(string $attribut)
    {
        switch ($attribut){
            case 'id':
                return $this->id_Facture;
                
            case 'creation':
                return $this->dateCreation;
            
            case 'echeance':
                return $this->dateEcheance;
                
            case 'ht':
                return $this->ht;
                
            case 'tva':
                $tva = number_format($this->ht*0.2,2,'.','');
                return $tva;
                
            case 'ttc':
                $ttc = number_format($this->ht+$this->ht*0.2,2,'.','');
                return $ttc;
                
            case 'accompte':
                return $this->accompte;
                
            case 'net_apayer':
                $net_apayer = ($this->ht+$this->ht*0.2)-$this->accompte;
                return number_format($net_apayer,2,'.','');
                
            case 'etat':
                return $this->etat;
                
            case 'client':
                return $this->le_cli;
                
            case 'produits':
                return $this->lesProduits;
                
            case 'moyen':
                return $this->moyen_paiement;
                
            default:
                return '';
               
        }
    }
}

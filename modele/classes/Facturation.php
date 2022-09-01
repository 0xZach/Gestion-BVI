<?php
/*
 * Description de Facturation.php
 *
 * auteur MASCLET Sylvain
 * Creation 07-02-2022
 * Derniere MAJ 07/02/2022
 * Classe décrivant une Facturation
 * 
 */

class Facturation {
    // Attributs
    private $laFacture;
    private $leProduit;
    private $laQuantite;
    private $laRemise;
    private $ordre;
    
    // Constructeur
    public function __construct(Facture $p_fact,Produit $p_prod,int $p_qte,float $p_rem, int $p_ordre) {
        $this->laFacture=$p_fact;
        $this->leProduit=$p_prod;
        $this->laQuantite=$p_qte;
        $this->laRemise=$p_rem;
        $this->ordre=$p_ordre;
    }
    
    
    // Gets & Sets
    public function getFacture(){
        return $this->laFacture;
    }
    
    public function getProduit(){
        return $this->leProduit;
    }
    
    public function getQte(){
        return $this->laQuantite;
    }
    
    public function getRemise(){
        return $this->laRemise;
    }
    
    public function getOrdre(){
        return $this->laRemise;
    }
    
    
    //------------------------------------//
    

    public function setFacture(string $p){
        $this->laFacture=$p;
    }
    
    public function setProduit(string $p){
        $this->leProduit=$p;
    }
    
    public function setQte(int $p){
        $this->laQuantite=$p;
    }
    
    public function setRemise(float $p){
        $this->laRemise=$p;
    }
    
    
    // fonctions
    public function getAttribut(string $attribut)
    {
        switch ($attribut){
            case 'facture':
                return $this->laFacture;
                
            case 'produit':
                return $this->leProduit;
                
            case 'quantite':
                return $this->laQuantite;
            
            case 'remise':
                return $this->laRemise;
                
            case 'ordre':
                return $this->ordre;
                
            default:
                return '';
                
        }
    }
    
}

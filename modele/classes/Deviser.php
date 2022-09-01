<?php
/*
 * Description de Deviser.php
 *
 * auteur MASCLET Sylvain
 * Creation 07-02-2022
 * Derniere MAJ 07/02/2022
 * Classe décrivant le fait de Deviser
 * 
 */

class Deviser {
    // Attributs
    private $leDevis;
    private $leProduit;
    private $laQuantite;
    private $laRemise;
    private $ordre;
    
    // Constructeur
    public function __construct(Devis $p_dev,Produit $p_prod,int $p_qte,float $p_rem, int $p_ordre) {
        $this->leDevis=$p_dev;
        $this->leProduit=$p_prod;
        $this->laQuantite=$p_qte;
        $this->laRemise=$p_rem;
        $this->ordre=$p_ordre;
    }
    
    
    // Gets & Sets
    public function getDevis(){
        return $this->leDevis;
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
    

    public function setDevis(string $p){
        $this->leDevis=$p;
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
            case 'devis':
                return $this->leDevis;
                
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

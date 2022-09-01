<?php
/*
 * Description de Produit.php
 *
 * auteur MASCLET Sylvain
 * Creation 07-02-2022
 * Derniere MAJ 07/03/2022
 * Classe décrivant un Produit
 * 
 */

class Produit {
    // Attributs
    private $id_produit;
    private $libelle;
    private $pu_ht;
    
    // Constructeur
    protected function __construct(string $p_prod,string $p_libelle,float $p_pu) {
        $this->id_produit=$p_prod;
        $this->libelle=$p_libelle;
        $this->pu_ht=$p_pu;
    }
    
    
    // Gets & Sets
    public function getId(){
        return $this->id_produit;
    }
    
    public function getLibelle(){
        return $this->libelle;
    }
    
    public function getPuHT(){
        return $this->pu_ht;
    }
    
    public function getPuTTC(){
        return $this->pu_ht*1.2;
    }
    
    
    //------------------------------------//
    

    public function setId(string $p){
        $this->id_produit=$p;
    }
    
    public function setLibelle(string $p){
        $this->libelle=$p;
    }
    
    public function setPu(float $p){
        $this->pu_ht=$p;
    }
    
    
    
    // fonctions
    public function getAttribut(string $attribut)
    {
        switch ($attribut){
            case 'id':
                return $this->id_produit;
                
            case 'libelle':
                return $this->libelle;
                
            case 'ht':
                return $this->pu_ht;
                
            case 'ttc':
                return number_format($this->pu_ht*1.2,2,'.','');
                
            default:
                return '';
                
        }
    }
    
}

<?php
/*
 * Description d'Avoir.php
 *
 * auteur MASCLET Sylvain
 * Creation 07-03-2022
 * Derniere MAJ 08/03/2022
 * Classe décrivant un Avoir
 * 
 */


class Avoir{
    // Attributs
    private $id_avoir;
    private $rembourser;
    private $qui_rembourser;
    private $raison;
    private $description;
    
    // Constructeur
    public function __construct(string $p_id, float $p_rembourser, Facture $p_fact, string $p_raison, string $p_desc) {
        $this->id_avoir = $p_id;
        $this->rembourser = $p_rembourser;
        $this->qui_rembourser = $p_fact;
        $this->raison = $p_raison;
        $this->description = $p_desc;
    }
    
    
    // Gets & Sets
    public function getId(){
        return $this->id_avoir;
    }
    
    public function getRemboursement(){
        return $this->rembourser;
    }
    
    public function getQuiRembourser(){
        return $this->qui_rembourser;
    }
    
    public function getRaison(){
        return $this->raison;
    }
    
    public function getDesc(){
        return $this->description;
    }


    //------------------------------------//
    
    public function setId(string $p){
        $this->id_avoir = $p;
    }
    
    public function setRemboursement(float $p){
        $this->rembourser = $p;
    }
    
    public function setQuiRembourser(Facture $p){
        $this->qui_rembourser = $p;
    }
    
    public function setRaison(string $p){
        $this->raison = $p;
    }
    
    public function setDesc(string $p){
        $this->description = $p;
    }
    
    
    
    // fonctions
    public function getAttribut(string $attribut){
        switch($attribut){
            case 'id':
                return $this->id_avoir;
            
            case 'rembourser':
                return $this->rembourser;
            
            case 'facture':
                return $this->qui_rembourser;
                
            case 'raison':
                return $this->raison;
            
            case 'description':
                return $this->description;
        }
    }
    
}

<?php
/*
 * Description de Service.php
 *
 * auteur MASCLET Sylvain
 * Creation 07-02-2022
 * Derniere MAJ 14/02/2022
 * Classe décrivant un Produit
 * 
 */


class Service extends Produit{
    // Attributs
    private $taux_horaire;
    
    // Constructeur
    public function __construct(string $p_prod,string $p_libelle,float $p_pu,bool $p_txHoraire) {
        parent::__construct($p_prod, $p_libelle, $p_pu);
        $this->taux_horaire=$p_txHoraire;
    }
    
    
    // Gets & Sets
    public function getTaux(){
        return $this->taux_horaire;
    }
    
    
    //------------------------------------//
    
    
    public function setTaux(bool $p_txHoraire){
        $this->taux_horaire=$p_txHoraire;
    }
    
    
    // fonctions
    
    
}

<?php
/*
 * Description de Bien.php
 *
 * auteur MASCLET Sylvain
 * Creation 07-02-2022
 * Derniere MAJ 14/02/2022
 * Classe décrivant un Bien
 * 
 */


class Bien extends Produit{
    // Attributs
    private $code_barre;
    
    // Constructeur
    public function __construct(string $p_prod,string $p_libelle,float $p_pu,string $p_code) {
        parent::__construct($p_prod, $p_libelle, $p_pu);
        $this->code_barre = $p_code;
    }
    
    
    // Gets & Sets
    public function getCodeBarre(){
        return $this->code_barre;
    }
    
    //------------------------------------//
    
    public function setCodeBarre(string $p){
        $this->code_barre = $p;
    }
    
    // fonctions
    
    
}

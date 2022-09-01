<?php
/*
 * Description de Echeancier.php
 *
 * auteur MASCLET Sylvain
 * Creation 10-03-2022
 * Derniere MAJ 10/03/2022
 * Classe décrivant un Échéancier
 * 
 */


class Echeancier{
    
    
    
    /* ATTRIBUTS */
    
    private $id;
    private $laFacture;
    private $reste_apayer;
    private $etat;
    private $dateValidation;
    
    
    
    /* CONSTRUCTEUR */
    
    public function __construct(string $p_id, Facture $p_fact, float $p_apayer, string $p_etat, string $p_date) {
        $this->id = $p_id;
        $this->laFacture = $p_fact;
        $this->reste_apayer = $p_apayer;
        $this->etat = $p_etat;
        $this->dateValidation = $p_date;
    }
    
    
    
    /* Gets & Sets */
    
    public function getId(){
        return $this->id;
    }
    
    public function getFacture(){
        return $this->laFacture;
    }
    
    public function getReste(){
        return $this->reste_apayer;
    }
    
    public function getEtat(){
        return $this->etat;
    }
    
    public function getDateValid(){
        return $this->dateValidation;
    }


    //------------------------------------//
    
    
    public function setId(string $p){
        $this->id = $p;
    }
    
    public function setFacture(string $p){
        $this->laFacture = $p;
    }
    
    public function setReste(string $p){
        $this->reste_apayer = $p;
    }
    
    public function setEtat(string $p){
        $this->etat = $p;
    }
    
    public function setDateValid(string $p){
        $this->dateValidation = $p;
    }
    
    
    
    /* FONCTIONS */
    
    /*
     * @Nom: getAttribut
     * 
     * @Paramètres: 
     * string $attribut
     * 
     * @Usage:
     * Cette fonction permet de remplacer les fonctions Get en renvoyant
     * l'attribut demandé en paramètre via un switch sur la variable $attribut.
     * 
     * @Return:
     * Cette fonction retourne une variable de type variable.
     * 
     */
    public function getAttribut(string $attribut)
    {
        switch($attribut){
            case 'id':
                return $this->id;
                
            case 'facture':
                return $this->laFacture;
            
            case 'reste_apayer':
                return $this->reste_apayer;
                
            case 'etat':
                return $this->etat;
                
            case 'dateValidation':
                return $this->dateValidation;
                
            default:
                return '';
        }
    }
    
}

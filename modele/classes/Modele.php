<?php
/*
 * Description de Modele.php
 *
 * auteur MASCLET Sylvain
 * Creation 10-03-2022
 * Derniere MAJ 10/03/2022
 * Classe Modele
 * 
 */


class Modele{
    
    
    
    /* ATTRIBUTS */
    
    private $test;
    
    
    
    /* CONSTRUCTEUR */
    
    public function __construct(string $p_test) {
        $this->test = $p_test;
    }
    
    
    
    /* Gets & Sets */
    
    public function getTest(){
        return $this->test;
    }


    //------------------------------------//
    
    public function setTest(string $p){
        $this->test = $p;
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
            case 'test':
                return $this->test;
                
            default:
                return '';
        }
    }
    
}

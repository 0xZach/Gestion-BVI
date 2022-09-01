<?php
/*
 * Description de Client.php
 *
 * auteur MASCLET Sylvain
 * Creation 31-01-2022
 * Derniere MAJ 31/01/2022
 * Classe décrivant un client
 */

class Client{
    
    // Attributs
    private $id_cli;
    private $nom;
    private $adresse;
    private $tel_fix;
    private $tel_mobile;
    private $mel;
    private $ville;
    private $codePostal;
    

    // Constructeur
    public function __construct(string $p_Id, string $p_nom,string $p_adr,string $p_fix,string $p_mobile,string $p_mel,string $p_ville,string $p_codep) {
        $this->id_cli=$p_Id;
        $this->nom=$p_nom;
        $this->adresse=$p_adr;
        $this->tel_fix=$p_fix;
        $this->tel_mobile=$p_mobile;
        $this->mel=$p_mel;
        $this->ville=$p_ville;
        $this->codePostal=$p_codep;
    }

    
    // Gets & Sets
    public function getId(){
        return $this->id_cli;
    }
    
    public function getNom(){
        return $this->nom;
    }
    
    public function getAdresse(){
        return $this->adresse;
    }
    
    public function getTelFix(){
        return $this->tel_fix;
    }
    
    public function getTelMobile(){
        return $this->tel_mobile;
    }
    
    public function getMel(){
        return $this->mel;
    }
    
    public function getVille(){
        return $this->ville;
    }
    
    public function getCodePostal(){
        return $this->codePostal;
    }
    
    //------------------------------------//
    
    public function setId(string $p_id){
        $this->id_cli=$p_id;
    }
    
    public function setNom(string $p_nom){
        $this->nom=$p_nom;
    }
    
    public function setAdresse(string $p_adresse){
        $this->adresse=$p_adresse;
    }
    
    public function setTelFix(string $p_fix){
        $this->tel_fix=$p_fix;
    }
    
    public function setMobile(string $p_mobile){
        $this->tel_mobile=$p_mobile;
    }
    
    public function setMel(string $p_mel){
        $this->mel=$p_mel;
    }
    
    public function setVille(string $p_ville){
        $this->ville=$p_ville;
    }
    
    public function setCodePostal(string $p_codeP){
        $this->codePostal=$p_codeP;
    }
    
    
    
    
    // Fonctions
    
    
    public function getAttribut(string $nomAttribut): string
    {
       switch($nomAttribut){
            case 'id':
                return $this->id_cli;

            case 'nom':
                return $this->nom;

            case 'ville':
                return $this->ville;

            case 'codepostal':
                return $this->codePostal;

            case 'adresse':
                return $this->adresse;

            case 'tel_fix':
                return $this->tel_fix;
                
            case 'tel_mobile':
                return $this->tel_mobile;
                
            case 'mel':
                return $this->mel;
                
            default:
                return "";
                
       } 
    }
}



?>
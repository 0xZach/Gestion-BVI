<?php
/*
 * Description de DAO.php
 *
 * auteur MASCLET Sylvain
 * Creation 31-01-2022
 * Derniere MAJ 10/03/2022
 * Classe permettant l'accès à l'authnetification dans la bdd
 */

class DAO {
    // Attributs
    protected $pdoAccess;
    private $server_name;
    private $db_name;
    private $usr_pdo;
    private $mdp_pdo;
    
    // Constructeur
    protected function __construct() {
        $this->server_name="localhost";
        $this->usr_pdo="etu20masclet";
        $this->mdp_pdo="fibule{32";
        $this->db_name="etu20masclet4";
        $this->pdoAccess = new PDO("mysql:host=$this->server_name;dbname=$this->db_name", $this->usr_pdo, $this->mdp_pdo);
    }
    
    // Fonctions
}
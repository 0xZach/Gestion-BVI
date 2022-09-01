<?php
/*
 * Description de AuthUsr_DAO.php
 *
 * auteur MASCLET Sylvain
 * Creation 31-01-2022
 * Derniere MAJ 25/02/2022
 * Classe permettant l'accès à l'authnetification dans la bdd
 */

require_once 'DAO.php';

class AuthUsr_DAO extends DAO {
    // Attributs
    
    // Constructeur
    function __construct() {
        parent::__construct();
    }
    
    // Fonctions
    
    // fonction vérifiant si le mot de passe est correcte par rapport à la base de données
    private function isPassOk($mdp):bool
    {
        try{
            $sth = $this->pdoAccess->prepare('SELECT authPass(:mdp) as isOk;');
            $sth->bindValue('mdp',$mdp,PDO::PARAM_STR);
            $sth->execute();
            $result=$sth->fetch();
            return $result['isOk'] ? true : false;
        } catch (Exception $e) {
            echo 'connexion failed';
        }
    }
    
    
    // fonction vérifiant si la session existe toujours
    public static function isLoggedOn($action): string
    {
        if (!isset($_SESSION)) session_start();
        
        // si la variable existe, on renvoi vers le fichier mit en paramètre
        // sinon, on renvoi à la page d'authentification
        if(isset($_SESSION['mdpOK']))
            return $action;
        else
            return "controleur/c_auth";
    }
    
    
    
    // fonction permettant de se connecter en tant qu'utilisateur
    public static function login($passW): bool
    {
        $toReturn=false;
        $access = new AuthUsr_DAO();
        
        
        // on vérifie si le mot de passe correspond bien à celui dans la base de données
        if($access->isPassOk($passW)){
            // puis on créé une variable de session pour se souvenir de la connexion
            if(!isset($_SESSION)){
                session_start();
                $_SESSION['mdpOK'] = true;
            }
            $toReturn=true;
        }
        return $toReturn;
    }
    
    
    
    // fonction permettant de se déconnecter
    public static function logout(): void
    {
        if(!isset($_SESSION))session_start();
        $_SESSION = array(); // remet les variables de session à 0
    }
}

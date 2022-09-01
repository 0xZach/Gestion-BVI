<?php
/*
 * Description de Echeancier_DAO.php
 *
 * auteur MASCLET Sylvain
 * Creation 31-01-2022
 * Derniere MAJ 10/03/2022
 * Classe permettant l'accès aux Échéances dans la bdd
 */

require_once "$racine/modele/classes/Echeancier.php";
require_once "$racine/modele/DAO/Facture_DAO.php";
require_once 'DAO.php';


class Echeancier_DAO extends DAO{
    // Attributs
    
    // Constructeur
    function __construct() {
        parent::__construct();
    }    
    
    
    
    // Fonctions
    
    
    public function getLesEcheances(string $like='',array $interval=array()): array
    {
        $query = 'SELECT e.* FROM Echeancier e '
                . 'Join Facture f on f.id_facture = e.la_facture '
                . 'WHERE (e.id_echeance like :recherche OR '
                . 'e.la_facture like :recherche OR '
                . 'e.dateValidation like :recherche OR '
                . 'e.etat like :recherche OR '
                . 'e.reste_apayer like :recherche) ';
        if($interval !== array())
            $query .= 'AND f.dateEcheance BETWEEN :dateDebut AND :dateFin '
                . 'ORDER BY e.la_facture DESC;';
        try{
            $sth = $this->pdoAccess->prepare($query);
            $sth->bindValue(':recherche', '%'.$like.'%', PDO::PARAM_STR);
            if($interval !== array()){
                $sth->bindValue(':dateDebut', $interval[0], PDO::PARAM_STR);
                $sth->bindValue(':dateFin', $interval[1], PDO::PARAM_STR);
            }
            $sth->execute();
            $result=$sth->fetchAll();
            return $this->dataToArrayEcheances($result);
        } catch (Exception $e) {
            echo 'connexion failed';
        }
    }
    
    
    
    
    
    
    
    
    public function dataToArrayEcheances(array $data): array
    {
        $factAccess = new Facture_DAO();
        $lesEcheances = array();
        
        for ($i=0;$i < sizeof($data);$i++) {
            if($data[$i]['dateValidation'] === null)
                $dateValidation = '';
            else
                $dateValidation = $data[$i]['dateValidation'];
            
            $laFacture = $factAccess->getLesFactures($data[$i]['la_facture'])[0];
            
            $lesEcheances[] = new Echeancier($data[$i]['id_echeance'],$laFacture,floatval($data[$i]['reste_apayer']),$data[$i]['etat'],$dateValidation);
        }
        return $lesEcheances;
    }
    
    
    
    
    
    
    
    public function ajoutEcheance(array $infosEcheances): string
    {
        try{
            $sth = $this->pdoAccess->prepare("INSERT INTO Echeancier VALUES((SELECT newEcheanceId()),:id_facture,:reste_apayer,'EE',null);");
            foreach ($infosEcheances as $key => $value) {
                $sth->bindValue(':'.$key,$value,PDO::PARAM_STR);
            }
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        try{
            $sth = $this->pdoAccess->prepare('SELECT id_echeance FROM Echeancier ORDER BY id_echeance DESC LIMIT 1;');
            $sth->execute();
            return $sth->fetch()['id_echeance'];
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    public function modifEcheance(string $id,string $champ,string $nouvValeur): void
    {
        try{
            $sth = $this->pdoAccess->prepare('update Echeancier set '.$champ.'=:value where id_echeance=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->bindValue(':value',$nouvValeur,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    public function supprEcheance(string $id){
        try{
            $sth = $this->pdoAccess->prepare('DELETE FROM Echeancier WHERE id_echeance=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    public function modifDateValidation(string $id, string $nouvValeur){
        
        // on valide la facture
        try{
            $sth = $this->pdoAccess->prepare("update Echeancier set etat='EV' where id_echeance=:id and etat='EE';");
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        // puis on ajoute la date à la base de donnée
        try{
            $sth = $this->pdoAccess->prepare('update Echeancier set dateValidation=:value where id_echeance=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->bindValue(':value',$nouvValeur,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    public function getLesEcheancesParClient(string $id_echeance='', string $id_client=''){
        $query = 'SELECT * FROM echeancesParClient '
                . 'WHERE '
                . 'id_echeance like :id_echeance OR '
                . 'id_cli like :id_client '
                . 'ORDER BY id_cli DESC;';
        try{
            $sth = $this->pdoAccess->prepare($query);
            $sth->bindValue(':id_echeance','%'.$id_echeance.'%',PDO::PARAM_STR);
            $sth->bindValue(':id_client','%'.$id_client.'%',PDO::PARAM_STR);
            $sth->execute();
            $result=$sth->fetchAll();
            return $result;
        } catch (Exception $e) {
            echo 'connexion failed';
        }
    }
}

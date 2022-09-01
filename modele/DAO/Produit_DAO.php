<?php
/*
 * Description de Produit_DAO.php
 *
 * auteur MASCLET Sylvain
 * Creation 07-02-2022
 * Derniere MAJ 07/03/2022
 * Classe permettant l'accès aux produits,biens et services dans la bdd
 */

require_once "$racine/modele/classes/Produit.php";
require_once "$racine/modele/classes/Bien.php";
require_once "$racine/modele/classes/Service.php";
require_once 'DAO.php';

class Produit_DAO extends DAO{
    // Attributs
    
    // Constructeur
    function __construct() {
        parent::__construct();
    }
        
    // Fonctions
    public function getProduits(string $like=''): array
    {
        try{
        $sth = $this->pdoAccess->prepare(
                "SELECT id_produit, libelle, pu_ht, ifnull(code_barre,'') as code_barre, tx_horaire "
                . "FROM lesProduits "
                . "WHERE "
                . "id_produit like :recherche OR "
                . "libelle like :recherche OR "
                . "pu_ht like :recherche OR "
                . "tx_horaire like :recherche OR "
                . "code_barre like :recherche "
                . "ORDER BY id_produit DESC;");
        $sth->bindValue(':recherche', '%'.$like.'%', PDO::PARAM_STR);
        $sth->execute();
        $result=$sth->fetchAll();
        return $this->dataToArrayProduits($result);
        } catch (Exception $e) {
            echo 'connexion failed';
        }
    }


    public function dataToArrayProduits(array $data): array
    {
        $lesProduits = array();
        for ($i=0;$i < sizeof($data);$i++) {
            if($data[$i]['tx_horaire'] === null)
            {
                $lesProduits[] = new Bien(
                    $data[$i]['id_produit'], $data[$i]['libelle'], floatval($data[$i]['pu_ht']),$data[$i]['code_barre']
                );
            }
            else
            {
                $lesProduits[] = new Service(
                    $data[$i]['id_produit'], $data[$i]['libelle'], floatval($data[$i]['pu_ht']),
                    boolval($data[$i]['tx_horaire'])  
                );
            }
            
        }
        return $lesProduits;
    }
    
    
    public function ajoutBien(array $infosBien): string
    {
        try{
            $sth = $this->pdoAccess->prepare('insert into Produit values ((SELECT newProdId()),:libelle,:pu_ht);');
            $sth->bindValue(':libelle',$infosBien['libelle'],PDO::PARAM_STR);
            $sth->bindValue(':pu_ht',$infosBien['pu_ht'],PDO::PARAM_STR);
            
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        try{
            $sth = $this->pdoAccess->prepare('insert into Bien VALUES ((SELECT id_produit FROM Produit ORDER BY id_produit DESC LIMIT 1),:code_barre);');
            $sth->bindValue(':code_barre',$infosBien['code_barre'],PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        try{
            $sth = $this->pdoAccess->prepare('SELECT id_produit FROM Produit ORDER BY id_produit DESC LIMIT 1;');
            $sth->execute();
            return $sth->fetch()['id_produit'];
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    public function modifBien(string $id,string $champ,string $nouvValeur): void
    {
        try{
            $sth = $this->pdoAccess->prepare('update Bien set '.$champ.'=:value where id_type=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->bindValue(':value',$nouvValeur,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    public function ajoutService(array $infosService): string
    {
        try{
            $sth = $this->pdoAccess->prepare('insert into Produit values ((SELECT newProdId()),:libelle,:pu_ht);');
            $sth->bindValue(':libelle',$infosService['libelle'],PDO::PARAM_STR);
            $sth->bindValue(':pu_ht',$infosService['pu_ht'],PDO::PARAM_STR);
            $sth->execute();
            
        } catch (Exception $ex) {
            $ex->getMessage();
        }
        
        try{
            $sth = $this->pdoAccess->prepare('SELECT id_produit FROM Produit ORDER BY id_produit DESC LIMIT 1;');
            $sth->execute();
            $id_prod = $sth->fetch()['id_produit'];
            
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        try{
            $sth = $this->pdoAccess->prepare('insert into Service VALUES (:id_produit,:tx_horaire);');
            $sth->bindValue(':id_produit',$id_prod,PDO::PARAM_STR);
            $sth->bindValue(':tx_horaire', $infosService['tx_horaire'],PDO::PARAM_BOOL);
            $sth->execute();
            
            return $id_prod;
            
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        
    }
    
    
    
    public function modifService(string $id,string $champ,string $nouvValeur): void
    {
        try{
            $sth = $this->pdoAccess->prepare('update Service set '.$champ.'=:value where id_type=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->bindValue(':value',$nouvValeur);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    public function modifProduit(string $id,string $champ,string $nouvValeur): void
    {
        switch($champ){
            case 'code_barre':
                $this->modifBien($id, $champ, $nouvValeur);
                break;
            case 'tx_horaire':
                $this->modifService($id, $champ, $nouvValeur);
                break;
            default:
                try{
                    $sth = $this->pdoAccess->prepare('update Produit set '.$champ.'=:value where id_produit=:id;');
                    $sth->bindValue(':id',$id,PDO::PARAM_STR);
                    $sth->bindValue(':value',$nouvValeur,PDO::PARAM_STR);
                    $sth->execute();
                } catch (Exception $ex) {
                    echo $ex->getMessage();
                }
                break;
                
        }
        
    }
    
    
    public function supprProduit(string $id){
        try{
            $sth = $this->pdoAccess->prepare('call deleteProduit(:id);');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
}
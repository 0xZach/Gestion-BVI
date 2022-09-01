<?php
/*
 * Description de Client_DAO.php
 *
 * auteur MASCLET Sylvain
 * Creation 31-01-2022
 * Derniere MAJ 14/02/2022
 * Classe permettant l'accès aux clients dans la bdd
 */

require_once "$racine/modele/classes/Client.php";
require_once 'DAO.php';

class Client_DAO extends DAO{
    // Attributs
    
    // Constructeur
    function __construct() {
        parent::__construct();
    }    
    
    
    
    // Fonctions
    
    
    public function getLesClients(string $like=''): array
    {
        try{
            $sth = $this->pdoAccess->prepare('SELECT id_cli,nom,adresse,tel_fix,mel,tel_mobile,ville,codepostal '
                    . 'FROM Client '
                    . 'WHERE '
                    . 'id_cli like :recherche OR '
                    . 'nom like :recherche OR '
                    . 'adresse like :recherche OR '
                    . 'tel_fix like :recherche OR '
                    . 'mel like :recherche OR '
                    . 'tel_mobile like :recherche OR '
                    . 'ville like :recherche OR '
                    . 'codepostal like :recherche '
                    . 'ORDER BY id_cli DESC;');
            $sth->bindValue(':recherche', '%'.$like.'%', PDO::PARAM_STR);
            $sth->execute();
            $result=$sth->fetchAll();
            return $this->dataToArrayClient($result);
        } catch (Exception $e) {
            echo 'connexion failed';
        }
    }
    
    
    
    public function dataToArrayClient(array $data): array
    {
        $lesClients = array();
        for ($i=0;$i < sizeof($data);$i++) {
            $lesClients[] = new Client(
                    $data[$i]['id_cli'], $data[$i]['nom'], $data[$i]['adresse'],
                    $data[$i]['tel_fix'], $data[$i]['tel_mobile'], $data[$i]['mel'], $data[$i]['ville'], $data[$i]['codepostal']);
        }
        return $lesClients;
    }
    
    
    
    public function ajoutClient(array $infosClient): string
    {
        try{
            $sth = $this->pdoAccess->prepare('INSERT INTO Client VALUES((SELECT newCliId()),:nom,:adresse,:fix,:mobile,:mel,:ville,:codepostal);');
            foreach ($infosClient as $key => $value) {
                $sth->bindValue(':'.$key,$value,PDO::PARAM_STR);
            }
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        try{
            $sth = $this->pdoAccess->prepare('SELECT id_cli FROM Client ORDER BY id_cli DESC LIMIT 1;');
            $sth->execute();
            return $sth->fetch()['id_cli'];
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    public function modifClient(string $id,string $champ,string $nouvValeur): void
    {
        try{
            $sth = $this->pdoAccess->prepare('update Client set '.$champ.'=:value where id_cli=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->bindValue(':value',$nouvValeur,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    public function supprClient(string $id){
        try{
            $sth = $this->pdoAccess->prepare('DELETE FROM Client WHERE id_cli=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
}
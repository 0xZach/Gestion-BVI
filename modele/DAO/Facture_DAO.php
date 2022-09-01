<?php
/*
 * Description de Facture_DAO.php
 *
 * auteur MASCLET Sylvain
 * Creation 11-02-2022
 * Derniere MAJ 02/03/2022
 * Classe permettant l'accès aux Factures dans la bdd
 */

require_once "$racine/modele/classes/Facture.php";
require_once "$racine/modele/classes/Facturation.php";
require_once "$racine/modele/DAO/Client_DAO.php";
require_once "$racine/modele/DAO/Produit_DAO.php";
require_once "$racine/modele/DAO/Echeancier_DAO.php";
require_once "$racine/modele/classes/Avoir.php";
require_once 'DAO.php';


class Facture_DAO extends DAO{
    // Attributs
    
    // Constructeur
    function __construct() {
        parent::__construct();
    }
    
    
    
    /* FONCTIONS */
    
    public function getLesFactures(string $like='',array $interval=array()): array
    {
        $query = 'SELECT * FROM Facture '
                . 'WHERE (id_facture like :recherche OR '
                . 'dateCreation like :recherche OR '
                . 'dateEcheance like :recherche OR '
                . 'mtnt_ht like :recherche OR '
                . 'accompte like :recherche OR '
                . 'moyen_paiement like :recherche OR '
                . 'etat like :recherche OR '
                . 'le_cli like :recherche) ';
        if($interval !== array())
            $query .= 'AND dateCreation BETWEEN :dateDebut AND :dateFin ';
        
        $query .= 'ORDER BY id_facture DESC;';
        
        
        try{
            $sth = $this->pdoAccess->prepare($query);
            $sth->bindValue(':recherche', '%'.$like.'%', PDO::PARAM_STR);
            if($interval !== array()){
                $sth->bindValue(':dateDebut', $interval[0], PDO::PARAM_STR);
                $sth->bindValue(':dateFin', $interval[1], PDO::PARAM_STR);
            }
            $sth->execute();
            $result=$sth->fetchAll();
            return $this->dataToArrayFacture($result);
        } catch (Exception $e) {
            echo 'connexion failed';
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function dataToArrayFacture(array $data): array
    {
        $lesFactures = array();
        $clientAccess = new Client_DAO();
        for ($i=0;$i < sizeof($data);$i++) {
            $lafacture = new Facture(
                    $data[$i]['id_facture'], $data[$i]['dateCreation'], $data[$i]['mtnt_ht'],
                    $data[$i]['accompte'], $data[$i]['etat'], $data[$i]['moyen_paiement'], $data[$i]['dateEcheance']);
            $leClient = $clientAccess->getLesClients($data[$i]['le_cli'])[0];
            $lafacture->setClient($leClient);
            $lesFactures[] = $lafacture;
        }
        return $lesFactures;
    }
    
    
    
    
    
    
    
    
    
    
    public function dataToArrayFacturation(array $data): array
    {
        $lesFacturations = array();
        $prodAccess = new Produit_DAO();
        for ($i=0;$i < sizeof($data);$i++) {
            $laFacture = $this->getLesFactures($data[$i]['la_facture'])[0];
            $leProduit = $prodAccess->getProduits($data[$i]['un_produit'])[0];
            $lafacturation = new Facturation(
                    $laFacture, $leProduit, intval($data[$i]['qte']), floatval($data[$i]['remise']), intval($data[$i]['ordre'])
            );
            $lesFacturations[] = $lafacturation;
        }
        return $lesFacturations;
    }
    
    
    
    
    
    
    
    
    
    public function dataToArrayAvoir(array $data): array
    {
        $lesAvoirs = array();
        $clientAccess = new Client_DAO();
        for ($i=0;$i < sizeof($data);$i++) {
            $laFacture = new Facture(
                $data[$i]['id_facture'], $data[$i]['dateCreation'], $data[$i]['mtnt_ht'],
                $data[$i]['accompte'], $data[$i]['etat'], $data[$i]['moyen_paiement'],
                $data[$i]['dateEcheance']
            );
            $leClient = $clientAccess->getLesClients($data[$i]['le_cli'])[0];
            $laFacture->setClient($leClient);
            $lAvoir = new Avoir(
                    $data[$i]['id_avoir'], $data[$i]['rembourser'],$laFacture,
                    $data[$i]['raison'],$data[$i]['description']);
            $lesAvoirs[] = $lAvoir;
        }
        return $lesAvoirs;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function ajoutFacture(array $infosFacture): string
    {
        try{
            $sth = $this->pdoAccess->prepare(
                    'INSERT INTO Facture VALUES '
                    . '((select newFactId()), :dateCreation, :mtnt_ht, :accompte, :etat, :id_cli, :moyen_paiement, :dateFin);'
                   );
            foreach ($infosFacture as $key => $value) {
                $sth->bindValue(':'.$key,$value,PDO::PARAM_STR);
            }
            if(array_key_exists("dateFin",$infosFacture))
                $sth->bindValue(':dateFin',$infosFacture['dateFin'],PDO::PARAM_STR);
            else
                $sth->bindValue(':dateFin',$infosFacture['dateCreation'],PDO::PARAM_STR);
            
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        try{
            $sth = $this->pdoAccess->prepare('select id_facture FROM Facture ORDER BY id_facture DESC LIMIT 1;');
            $sth->execute();
            return $sth->fetch()['id_facture'];
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function modifFacture(string $id,string $champ,string $nouvValeur): void
    {
        try{
            $sth = $this->pdoAccess->prepare('update Facture set '.$champ.'=:value where id_facture=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->bindValue(':value',$nouvValeur,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function supprFacture(string $id){
        try{
            $sth = $this->pdoAccess->prepare('DELETE FROM Echeancier WHERE la_facture=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        try{
            $sth = $this->pdoAccess->prepare('DELETE FROM Facturer WHERE la_facture=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        try{
            $sth = $this->pdoAccess->prepare('DELETE FROM Facture WHERE id_facture=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
   
    
    
    
    
    
    
    
    
    
    
    public function ajoutFacturation(facture $p_fact, array $p_prod){
        for($i=0;$i < count($p_prod); $i++){
            try{
                $sth = $this->pdoAccess->prepare('INSERT INTO Facturer values(:facture,:produit,1,0,ajoutLigneFacture(:facture));');
                $sth->bindValue(':facture',$p_fact->getId(),PDO::PARAM_STR);
                $sth->bindValue(':produit', $p_prod[$i]->getId(), PDO::PARAM_STR);
                $sth->execute();
                
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        }
        
    }
    
    
    
    
    
    
    
    
    
    
    
    public function getFacturations(string $id_facture,string $id_produit=''){
        try{
            $sth = $this->pdoAccess->prepare(
                      'SELECT * FROM Facturer '
                    . 'WHERE la_facture like :id_facture AND un_produit like :id_produit '
                    . 'ORDER BY ordre;');
            $sth->bindValue(':id_facture','%'.$id_facture.'%',PDO::PARAM_STR);
            $sth->bindValue(':id_produit','%'.$id_produit.'%',PDO::PARAM_STR);
            $sth->execute();
            $result=$sth->fetchAll();
            return $this->dataToArrayFacturation($result);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function modifFacturation(string $id_facture,string $id_produit,string $champ,string $nouvValeur,int $ordre): void
    {
        try{
            $query= 'update Facturer set '.$champ.'=:value '
                  . 'where la_facture=:id_facture AND un_produit=:id_produit AND ordre=:ordre;';
            $sth = $this->pdoAccess->prepare($query);
            $sth->bindValue(':id_facture',$id_facture,PDO::PARAM_STR);
            $sth->bindValue(':id_produit',$id_produit,PDO::PARAM_STR);
            $sth->bindValue(':ordre',$ordre,PDO::PARAM_INT);
            $sth->bindValue(':value',$nouvValeur,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        
        
    }
    
    
    
    
    
    
    
    
    public function majMontantFacture(string $idFacture){
        $lesFacturations = $this->getFacturations($idFacture);
        $mtnt_ht =  0.0;
        for($i=0;$i < count($lesFacturations);$i++){
            $leProduit = $lesFacturations[$i]->getAttribut('produit');
            $laRemise = $leProduit->getAttribut('ht')*($lesFacturations[$i]->getAttribut('remise')/100);
            $mtnt_ht += $leProduit->getAttribut('ht') - $laRemise;
        }
        
        $mtnt_ht = number_format($mtnt_ht,2,'.','');
        
        $this->modifFacture($idFacture,'mtnt_ht', $mtnt_ht);
    }
    
    
    
    
    
    
    
    
    
    
    public function supprProduitFacturation(string $id_facture,string $id_produit,int $ordre){
        try{
            $sth = $this->pdoAccess->prepare(
                      'DELETE FROM Facturer '
                    . 'WHERE la_facture=:id_facture AND un_produit=:id_produit AND ordre=:ordre;');
            $sth->bindValue(':id_facture',$id_facture,PDO::PARAM_STR);
            $sth->bindValue(':id_produit',$id_produit,PDO::PARAM_STR);
            $sth->bindValue(':ordre',$ordre,PDO::PARAM_INT);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function getProduitsNonFacturer(string $id_facture){
        try{
            $sth = $this->pdoAccess->prepare(
                      "select id_produit, libelle, pu_ht, ifnull(code_barre,'') as code_barre,tx_horaire "
                    . "FROM lesProduits "
                    . "WHERE id_produit "
                    . "NOT IN (SELECT un_produit FROM Facturer WHERE la_facture = :id_facture);");
            $sth->bindValue(':id_facture',$id_facture,PDO::PARAM_STR);
            $sth->execute();
            $result=$sth->fetchAll();
            $produitAccess = new Produit_DAO();
            return $produitAccess->dataToArrayProduits($result);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    


    public function getLesAvoirs(string $like='',array $interval=array()){
        $query = 'SELECT * FROM lesAvoirs '
                    . 'WHERE (id_avoir like :recherche OR '
                    . 'id_facture like :recherche OR '
                    . 'dateCreation like :recherche OR '
                    . 'dateEcheance like :recherche OR '
                    . 'mtnt_ht like :recherche OR '
                    . 'accompte like :recherche OR '
                    . 'moyen_paiement like :recherche OR '
                    . 'etat like :recherche OR '
                    . 'le_cli like :recherche OR '
                    . 'rembourser like :recherche OR '
                    . 'raison like :recherche OR '
                    . 'description like :recherche) ';
        if($interval !== array())
            $query .= 'AND dateCreation BETWEEN :dateDebut AND :dateFin;';
        else
            $query .= ';';
        
        try{
            $sth = $this->pdoAccess->prepare($query);
            $sth->bindValue(':recherche', '%'.$like.'%', PDO::PARAM_STR);
            if($interval !== array()){
                $sth->bindValue(':dateDebut', $interval[0], PDO::PARAM_STR);
                $sth->bindValue(':dateFin', $interval[1], PDO::PARAM_STR);
            }
            $sth->execute();
            $result=$sth->fetchAll();
            return $this->dataToArrayAvoir($result);

        } catch (Exception $ex) {
            $ex->getMessage();
        }
    }










    public function ajoutAvoir(array $infosAvoir){       

        try{
            $sth = $this->pdoAccess->prepare(
                    'INSERT INTO Avoir VALUES '
                    . '((select newFactId()), :remboursement,:id_facture, :raison, :description);'
            );
            foreach($infosAvoir as $key => $value){
                $sth->bindValue(':'.$key,$value,PDO::PARAM_STR);
            }
            $sth->execute();
        } catch (Exception $ex) {
            $ex->getMessage();
        }
        
        try{
            $sth = $this->pdoAccess->prepare('select id_avoir FROM Avoir ORDER BY id_avoir DESC LIMIT 1;');
            $sth->execute();
            return $sth->fetch()['id_avoir'];
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }


    
    
    
    
    
    


    public function modifAvoir(string $id_avoir, string $champ, string $value): void
    {
        try{
            $query= 'update Avoir set '.$champ.'=:value '
                  . 'where id_avoir=:id_avoir;';
            $sth = $this->pdoAccess->prepare($query);
            $sth->bindValue(':id_avoir',$id_avoir,PDO::PARAM_STR);
            $sth->bindValue(':value',$value, PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
}
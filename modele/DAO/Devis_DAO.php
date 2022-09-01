<?php
/*
 * Description de Devis_DAO.php
 *
 * auteur MASCLET Sylvain
 * Creation 11-02-2022
 * Derniere MAJ 02/03/2022
 * Classe permettant l'accès aux devis dans la bdd
 */

require_once "$racine/modele/classes/Devis.php";
require_once "$racine/modele/classes/Deviser.php";
require_once "$racine/modele/DAO/Client_DAO.php";
require_once "$racine/modele/DAO/Produit_DAO.php";
require_once "$racine/modele/DAO/Facture_DAO.php";
require_once 'DAO.php';


class Devis_DAO extends DAO{
    
    
    
    /* ATTRIBUTS */
    
    
    
    
    
    /* CONSTRUCTEUR */
    
    function __construct() {
        parent::__construct();
    }
     


    /* FONCTIONS */
    
    /*
     * @Nom: getLesDevis
     * 
     * @Paramètres: 
     * string $like = '', array $interval = array()
     * 
     * @Usage:
     * Cette Fonction permet de récupérer les devis depuis la base de donnée en
     * fonction de deux paramètres:
     * - le premier permet de faire un 'where [champ] like %[$like]%'
     * - le deuxième permet de choisir un interval de dates dans lequel choisir
     * les devis.
     * Les deux paramètres peuvent êtres omis.
     * 
     * @Return:
     * array(Devis)
     * 
     */
    public function getLesDevis(string $like='',array $interval=array()): array
    {
        $query = 'SELECT * '
                . 'FROM Devis '
                . 'WHERE '
                . '(id_devis like :recherche OR '
                . 'dateCreation like :recherche OR '
                . 'net_apayer like :recherche OR '
                . 'date_valid_fin like :recherche) ';
        
        
        if($interval !== array())
            $query .= 'AND dateCreation BETWEEN :dateDebut AND :dateFin ';
        
        
        $query .= 'UNION '
                . 'select d.* from Devis d '
                . 'join Client c on d.le_cli = c.id_cli '
                . 'WHERE (c.nom like :recherche) ';
        
        
        if($interval !== array())
            $query .= 'AND dateCreation BETWEEN :dateDebut AND :dateFin ';
        
        
        $query .= 'ORDER BY id_devis DESC;';
        
        
        
        try{
            $sth = $this->pdoAccess->prepare($query);
            $sth->bindValue(':recherche', '%'.$like.'%', PDO::PARAM_STR);
            if($interval !== array()){
                $sth->bindValue(':dateDebut', $interval[0], PDO::PARAM_STR);
                $sth->bindValue(':dateFin', $interval[1], PDO::PARAM_STR);
            }
            $sth->execute();
            $result=$sth->fetchAll();
            return $this->dataToArrayDevis($result);
        } catch (Exception $e) {
            echo 'connexion failed';
        }
    }
    
    
    
    
    
    
    
    
    
    /*
     * @Nom: dataToArrayDevis 
     * 
     * @Paramètres: 
     * $data = array
     * 
     * @Usage:
     * Cette fonction transforme un tableau de tableaux assoc en tableau d'objets de type Devis.
     * 
     * @Return:
     * array(Devis)
     * 
     */ 
    public function dataToArrayDevis(array $data): array
    {
        $lesDevis = array();
        $ClientAccess = new Client_DAO();
        for ($i=0;$i < sizeof($data);$i++) {
            $ledevis = new Devis(
                    $data[$i]['id_devis'], $data[$i]['dateCreation'], $data[$i]['date_valid_fin'], $data[$i]['net_apayer'], $data[$i]['etat']);
            $leClient = $ClientAccess->getLesClients($data[$i]['le_cli'])[0];
            $ledevis->setClient($leClient);
            $lesDevis[] = $ledevis;
        }
        return $lesDevis;
    }
    
    
    
    
    
    
    
    
    
    public function dataToArrayDeviser(array $data): array
    {
        $lesDeviser = array();
        $prodAccess = new Produit_DAO();
        for ($i=0;$i < sizeof($data);$i++) {
            $leDevis = $this->getLesDevis($data[$i]['le_devis'])[0];
            $leProduit = $prodAccess->getProduits($data[$i]['un_produit'])[0];
            $leDeviser = new Deviser(
                    $leDevis, $leProduit, intval($data[$i]['qte']), floatval($data[$i]['remise']),intval($data[$i]['ordre'])
            );
            $lesDeviser[] = $leDeviser;
        }
        return $lesDeviser;
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function ajoutDevis(array $infosDevis): string
    {
        try{
            $sth = $this->pdoAccess->prepare(
                    'INSERT INTO Devis VALUES '
                    . '((select newDevId()), :dateCreation, :date_valid_fin, :net_apayer, :etat, :id_cli);'
                   );
            foreach ($infosDevis as $key => $value) {
                $sth->bindValue(':'.$key,$value,PDO::PARAM_STR);
            }
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        try{
            $sth = $this->pdoAccess->prepare('SELECT id_devis FROM Devis ORDER BY id_devis DESC LIMIT 1;');
            $sth->execute();
            return $sth->fetch()['id_devis'];
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function modifDevis(string $id,string $champ,string $nouvValeur): void
    {
        try{
            $sth = $this->pdoAccess->prepare('update Devis set '.$champ.'=:value where id_devis=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->bindValue(':value',$nouvValeur,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    public function majMontantDevis(string $id_devis){
        $lesDeviser = $this->getDeviser($id_devis);
        $ttc =  0.0;
        for($i=0;$i < count($lesDeviser);$i++){
            $leProduit = $lesDeviser[$i]->getAttribut('produit');
            $laRemise = $leProduit->getAttribut('ht')*($lesDeviser[$i]->getAttribut('remise')/100);
            $ttc += $leProduit->getAttribut('ht') - $laRemise;
        }
        
        $ttc = number_format($ttc*1.2,2,'.','');
        
        $this->modifFacture($id_devis,'net_apayer', $ttc);
    }
    
    
    
    
    
    
    
    
    
    
    public function supprDevis(string $id){
        try{
            $sth = $this->pdoAccess->prepare('DELETE FROM Deviser WHERE le_devis=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
        try{
            $sth = $this->pdoAccess->prepare('DELETE FROM Devis WHERE id_devis=:id;');
            $sth->bindValue(':id',$id,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    
    public function ajoutDeviser(Devis $p_dev, array $p_prod){
        for($i=0;$i < count($p_prod); $i++){
            try{
                $sth = $this->pdoAccess->prepare('INSERT INTO Deviser values(:devis,:produit,1,0,ajoutLigneDevis(:devis));');
                $sth->bindValue(':devis',$p_dev->getId(),PDO::PARAM_STR);
                $sth->bindValue(':produit', $p_prod[$i]->getId(), PDO::PARAM_STR);
                $sth->execute();
            } catch (Exception $ex) {
                echo $ex->getMessage();
            }
        }
    }
    
    
    
    
    
    
    
    
    
    public function getDeviser(string $id_devis,string $id_produit=''){
        try{
            $sth = $this->pdoAccess->prepare(
                      'SELECT * FROM Deviser '
                    . 'WHERE le_devis like :id_devis AND un_produit like :id_produit '
                    . 'ORDER BY ordre;');
            $sth->bindValue(':id_devis','%'.$id_devis.'%',PDO::PARAM_STR);
            $sth->bindValue(':id_produit','%'.$id_produit.'%',PDO::PARAM_STR);
            $sth->execute();
            $result=$sth->fetchAll();
            return $this->dataToArrayDeviser($result);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    public function modifDeviser(string $id_devis,string $id_produit,string $champ,string $nouvValeur,int $ordre): void
    {
        try{
            $query= 'update Deviser set '.$champ.'=:value '
                  . 'where le_devis=:id_devis AND un_produit=:id_produit AND ordre=:ordre;';
            $sth = $this->pdoAccess->prepare($query);
            $sth->bindValue(':id_devis',$id_devis,PDO::PARAM_STR);
            $sth->bindValue(':id_produit',$id_produit,PDO::PARAM_STR);
            $sth->bindValue(':ordre',$ordre,PDO::PARAM_INT);
            $sth->bindValue(':value',$nouvValeur,PDO::PARAM_STR);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    
    public function supprProduitDeviser(string $id_devis,string $id_produit,int $ordre){
        try{
            $sth = $this->pdoAccess->prepare(
                      'DELETE FROM Deviser '
                    . 'WHERE le_devis=:id_devis AND un_produit=:id_produit AND ordre=:ordre;');
            $sth->bindValue(':id_devis',$id_devis,PDO::PARAM_STR);
            $sth->bindValue(':id_produit',$id_produit,PDO::PARAM_STR);
            $sth->bindValue(':ordre',$ordre,PDO::PARAM_INT);
            $sth->execute();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
   
    
    
    
    
    
    
    
    
    
    public function getProduitsFacturer(string $id_devis){
        try{
            $sth = $this->pdoAccess->prepare(
                      "select lp.id_produit,lp.libelle,lp.pu_ht,ifnull(lp.code_barre,'') as code_barre,lp.tx_horaire, d.ordre "
                    . "FROM lesProduits lp "
                    . "JOIN Deviser d on d.un_produit = lp.id_produit "
                    . "WHERE d.le_devis = :id_devis "
                    . "ORDER BY ordre;");
            $sth->bindValue(':id_devis',$id_devis,PDO::PARAM_STR);
            $sth->execute();
            $result=$sth->fetchAll();
            $produitAccess = new Produit_DAO();
            return $produitAccess->dataToArrayProduits($result);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    
    public function getProduitsNonFacturer(string $id_devis){
        try{
            $sth = $this->pdoAccess->prepare(
                    "select id_produit, libelle, pu_ht, ifnull(code_barre,'') as code_barre,tx_horaire "
                    . "FROM lesProduits "
                    . "WHERE id_produit "
                    . "NOT IN (SELECT un_produit FROM Deviser WHERE le_devis = :id_devis) "
                    . "ORDER BY id_produit desc;");
            $sth->bindValue(':id_devis',$id_devis,PDO::PARAM_STR);
            $sth->execute();
            $result=$sth->fetchAll();
            $produitAccess = new Produit_DAO();
            return $produitAccess->dataToArrayProduits($result);
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }
    }
    
    
    
    
    
    
    
    
    
    
    
    public function fromDevToFact($id_devis){
        $factureAccess = new Facture_DAO();
        $leDevis = $this->getLesDevis($id_devis)[0];
        
        $infosFacture = array(
            "dateCreation" => $leDevis->getAttribut('creation'),
            "dateFin" => $leDevis->getAttribut('fin'),
            "etat" => 'FE',
            "mtnt_ht" => ($leDevis->getAttribut('net_apayer')/1.2),
            "accompte" => 0,
            "id_cli" => $leDevis->getAttribut('client')->getAttribut('id'),
            "moyen_paiement" => ""
        );
        
        // on ajoute une nouvelle facture et on la récupère
        $nouvFactureId = $factureAccess->ajoutFacture($infosFacture);
        $nouvFacture = $factureAccess->getLesFactures($nouvFactureId)[0];
        
        // on récupère les produits facturés
        $lesProduitsDeviser = $this->getProduitsFacturer($id_devis);
        
        
        
        // on ajoute tous les produits à la nouvelle facturation
        $factureAccess->ajoutFacturation($nouvFacture, $lesProduitsDeviser);
        
        
        return $nouvFacture;
    }
    
    
    
    
}

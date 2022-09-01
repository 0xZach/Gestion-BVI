<?php

require_once "modele/classes/Facture.php";
require_once "modele/classes/Echeancier.php";
require_once "modele/classes/Devis.php";
require_once "modele/classes/Avoir.php";
require_once "modele/DAO/Produit_DAO.php";
require_once "modele/DAO/Echeancier_DAO.php";
require_once "modele/DAO/Facture_DAO.php";
require_once "modele/DAO/Client_DAO.php";
require_once "modele/pdf/tfpdf/tfpdf.php";

class pdf extends tFPDF
{
    //private $lepdf;
    static private $largeur=210;
    static private $hauteur=297;
    static private $margin_left=12;
    static private $margin_up=10;
    static private $margin_right=210-12;
    private $angle=0;


    public function __construct() {
        
        
        /* INSTANCIATION DU PDF */
        parent::__construct("P", "mm", array(self::$largeur, self::$hauteur));

    }
    
    
    
    public function newPage($infos = null, Client $p_cli = null,array $p_prod = [],array $interval = []){
        
        
        $this->AddPage();
        
        if(gettype($infos) !== 'array'){
            switch($infos){
                case $infos instanceof Avoir:
                    $type = 'Avoir';
                    break;

                case $infos instanceof Facture:
                    $type = 'Facture';
                    break;

                case $infos instanceof Devis:
                    $type = 'Devis';
                    break;
            }
        }
        else
        {
            if($infos[0]->getAttribut('etat') === 'EE')
                $titre = 'Échéancier';
            else
                $titre = 'Règlement';
            $type = 'Echeance';
        }
        
        
        
        
        /* DEFINITION DES ATTRIBUTS NON-CHANGEANT UTILISÉS */
        
        $this->SetAutoPageBreak(true, 5);
        $this->AddFont("DejaVuSans","","DejaVuSansCondensed.ttf",true);
        $this->AddFont("DejaVuSans","B","DejaVuSansCondensed-Bold.ttf",true);
        $this->SetFillColor(214, 214, 214); // couleur de remplissage
        $this->SetDrawColor(230, 230, 230); // couleur des bords
        $this->SetLineWidth(.3); // taille des bords
        
        
        /* APPEL DES HEADER, BODY ET FOOTER DE LA PAGE */
        
        if($type !== 'Echeance'){
            
            $this->headFacture(
                    html_entity_decode($p_cli->getNom(),ENT_QUOTES),
                    $p_cli->getAdresse(),
                    $p_cli->getCodePostal()." ".html_entity_decode($p_cli->getVille(),ENT_QUOTES));
            
            $prix = $this->body($infos,$p_cli,$p_prod);
            
            $this->foot($prix,$type);
            
            /* AFFICHAGE À L'ÉTAT 'EN COURS' */
        
            $etat = $infos->getAttribut('etat');

            if($etat === 'DE' || $etat === 'FE'){
                $this->SetAlpha(0.5);
                $this->SetFont("DejaVuSans","B",54);
                $this->SetTextColor(214, 214, 214);
                $this->RotatedText(37, 200, 'En cours de rédaction', 45);

                $this->SetAlpha(1);
                $this->SetTextColor(0);
            }
            
        }
        else{
            $this->headEcheance($titre, $interval);
            $this->bodyEcheance($infos);
        }
    }
    
    
    
    
    
        private function headEcheance(string $titre, array $interval){
        
        
        $this->SetFont("DejaVuSans", "B", 12);
        $this->SetXY(self::$margin_left-2, self::$margin_up);
        $this->Write(5, "SASU BV Informatique");
        
        
        
        $this->SetFont("DejaVuSans", "B", 8);
        $this->SetX(self::$margin_right - 30);
        $this->Write(5, 'Fait le '.date('d/m/Y'));
        
        
        
        $this->SetFont("DejaVuSans", "B", 12);
        $titre = $titre." par client"; 
        $this->SetXY(intval((self::$largeur-50)/2),35);
        $this->Cell(50, 10, $titre, 1, 2, 'C', true);
        
        
        $this->SetFont("DejaVuSans", "", 8);
        $intervalstr = 'Du '.$interval[0].' Au '.$interval[1];
        $this->SetX((self::$largeur-40)/2);
        $this->Write(5, $intervalstr);
        $this->Ln();
        
    }
    
    
    
    
    
    
    
    private function bodyEcheance(Array $lesEcheances=array()){
        $echeanceAccess = new Echeancier_DAO();
        $factureAccess = new Facture_DAO();
        $clientAccess = new Client_DAO();
        
        $this->SetXY(5, 60);
        
        $lesEcheancesParClient = $echeanceAccess->getLesEcheancesParClient();

        /* HEADER */

        $this->SetFont("DejaVuSans", "B", 9);

        $header=array(
            [45,"Le client"],
            [35,"Date Échéance"],
            [30,"N° Facture"],
            [35,"Moyen Paiement"],
            [15,"TTC"],
            [15,"Réglé"],
            [15,"Solde dû"]);

        for($k=0;$k<count($header);$k++){
            $this->Cell($header[$k][0],7,$header[$k][1],1,0,"C",true);
        }
        $this->ln();
        $this->setX(5);
        
        
        foreach($lesEcheancesParClient as $infosEcheances){
            $lecheance = $echeanceAccess->getLesEcheances($infosEcheances['id_echeance'])[0];
            
            if(array_search($lecheance,$lesEcheances) !== false){
                
                $laFacture = $factureAccess->getLesFactures($infosEcheances['id_facture'])[0];
                $lecheance = $echeanceAccess->getLesEcheances($infosEcheances['id_echeance'])[0];
                $leClient = $clientAccess->getLesClients($infosEcheances['id_cli'])[0];


                /* BODY */

                $this->SetFont("DejaVuSans", "", 9);

                $montant_regle= floatval($laFacture->getAttribut('ttc')) - floatval($lecheance->getAttribut('reste_apayer'));
                $montant_regle = number_format($montant_regle,2,'.','');
                
                $row=array(
                    $leClient->getAttribut('id').' '.$leClient->getAttribut('nom'),
                    $laFacture->getAttribut('echeance'),
                    $laFacture->getAttribut('id'),
                    $laFacture->getAttribut('moyen'),
                    $laFacture->getAttribut('ttc'),
                    $montant_regle,
                    number_format(floatval($lecheance->getAttribut('reste_apayer')),2,'.','')
                );

                for($k=0;$k<count($row);$k++){
                    if($k <= 3)
                        $this->Cell($header[$k][0],7,$row[$k],1,0,"L",false);
                    else
                        $this->Cell($header[$k][0],7,$row[$k],1,0,"R",false);
                }
                $this->ln();
                $this->setX(5);
                
            }

        }        
        
    }
    
    
    
    
    
    
    
    
    private function headFacture(string $nom="",string $adresse="",string $ville=""){
        
        /* LOGO */
        $this->Image("css/images/logo_BVI.png",self::$margin_right-45,self::$margin_up,45,15);
        // params: file, x, y, width, height, type
        
        
        /* MENTIONS LÉGALES */
        $this->SetFont("DejaVuSans", "B", 12);
        // pour une raison que j"ignore, le premier txt est décalé de 2 mm de plus que le reste
        $this->SetXY(self::$margin_left-2, self::$margin_up);
        $this->Write(5, "SASU BV Informatique");
        
        $this->SetFont("DejaVuSans", "B", 9);
        $this->SetX(self::$margin_left);
        $this->Ln();
        $this->Write(5, "Centre Commercial espace Couture");
        
        $this->SetX(self::$margin_left);
        $this->Ln();
        $this->Write(5, "24660 Sanilhac");
        
        $this->SetX(self::$margin_left);
        $this->Ln();
        $this->Write(5, "Tél: 05 53 06 90 84");
        
        $this->SetX(self::$margin_left);
        $this->Ln();
        $this->Write(5, "Email: bvi24@orange.fr");
        
        
        /* INFOS CLIENT */
        $this->SetFont("DejaVuSans", "", 12);
        $this->SetXY(135,45);
        if($nom !== "")
            $this->Write(5, $nom);
        
        if($adresse !== ""){
            $this->Ln();
            $this->SetX(135);
            $this->Write(5, $adresse);
        }
        
        $this->Ln();
        $this->SetX(135);
        if($ville !== "")
            $this->Write(5, $ville);
    }











    private function body($infos,$client,$prods){
        
        
        
        /* TITRE */
        
        $this->SetFont("DejaVuSans", "B", 15);
        $this->SetXY(self::$margin_left,50);
        
        switch($infos){
            
            case $infos instanceof Facture:
                $title = "Facture";
                $dateFin = "Date Échéance";
                $valeurFin = $infos->getAttribut("echeance");
                $moyen= $infos->getAttribut("moyen");
                $accompte = $infos->getAttribut("accompte");
                $etat = $infos->getAttribut('etat');
                break;
            
            
            case $infos instanceof Devis:
                $title = "Devis";
                $dateFin= "Date de validité";
                $valeurFin = $infos->getAttribut("fin");
                $moyen= "";
                $accompte=0.00;
                $etat = $infos->getAttribut('etat');
                break;
            
            
            case $infos instanceof Avoir:
                $laFacture = $infos->getAttribut('facture');
                $title = "Avoir";
                $date = date('d-m-Y');
                $ttc_origine = $laFacture->getAttribut('ttc');
                $a_rembourser = -1 * $infos->getAttribut('rembourser');
                $net_apayer = $ttc_origine + $a_rembourser;
                $raison = $infos->getAttribut('raison');
                $description = $infos->getAttribut('description');
                break;
        }
        
        $this->Cell($this->GetStringWidth($title)+6,10,$title,0,2,"C",true);
        
        
        
        
        
        /* TABLE INFOS FACTURE */
        
        $this->SetXY(self::$margin_left,80);
        
        $this->SetFont("DejaVuSans","B",9);
        // Header
        if($title !== 'Avoir'){
            $header=array([25,"Numéro"],[25,"Date"],[25,"Code Client"],[25,$dateFin],[40,"Mode de règlement"]);
            $data= [[$infos->getAttribut("id"),$infos->getAttribut("creation"),$client->getAttribut("id"),$valeurFin,$moyen]];
            
        }
        else{
            $header=array([25,"Numéro"],[25,"Date"],[25,"Code Client"]);
            $data= [[$infos->getAttribut("id"),$date,$client->getAttribut("id")]];
        }
        
        $headerWidth=0;
        
        for($i=0;$i<count($header);$i++){
            $this->Cell($header[$i][0],7,$header[$i][1],1,0,"C",true);
            $headerWidth += $header[$i][0];
        }
            
        $this->ln();
        $this->setX(self::$margin_left);
        // Color and font restoration
        //$this->SetFillColor(224,235,255);
        $this->SetTextColor(0);
        $this->SetFont("DejaVuSans","",8);
        
        // Infos Facture
        $fill = false;
        
            
        foreach($data as $row)
        {
            for($i = 0; $i < count($row); $i++){
                $this->Cell($header[$i][0],6,$row[$i],"LR",0,"C",$fill);
            }
            
            $this->Ln();
            $this->setX(self::$margin_left);
        }
        // Closing line
        $this->Cell($headerWidth,0,"","T");
        
        
        
        
        
        
        /* TABLE INFOS PRODUITS */
        $this->SetXY(self::$margin_left,98);
        
        $this->SetFont("DejaVuSans","B",9);
        
        if($title !== 'Avoir'){
            // Header
            $header=array([60,"Description"],[15,"Qté"],[20,"P.U. HT"],[25,"Montant HT"],[15,"TVA"],[15,"% REM"],[28,"MONTANT TTC"]);

            $headerWidth = 0;

            for($i=0;$i<count($header);$i++){
                $this->Cell($header[$i][0],7,$header[$i][1],1,0,"C",true);
                $headerWidth += $header[$i][0];
            }

            $this->ln();
            $this->setX(self::$margin_left);
            // Color and font restoration
            //$this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont("DejaVuSans","",8);



            // Infos Produits
            $fill = false;
            $data= array();
            $prodAccess = new Produit_DAO();
            $htCumule = $tvaCumule = $ttcCumule = 0.00;
            for($i=0;$i<count($prods);$i++){
                if($prods[$i]->getAttribut("produit")->getAttribut("id") !== "PR00000"){
                    $leprod= $prods[$i]->getAttribut("produit");
                    $mtnt_ht = $prods[$i]->getAttribut("quantite")*$leprod->getAttribut("ht");
                    $htCumule += $mtnt_ht;
                    $tva = number_format($mtnt_ht*0.2,2,".","");
                    $tvaCumule += $tva;
                    $remise= $prods[$i]->getAttribut("remise");
                    $ttcMax=$mtnt_ht+$tva;
                    $ttc = $ttcMax-$ttcMax*($remise/100);
                    $ttc = number_format($ttc,2,".","");
                    $ttcCumule += $ttc;
                    $data[]= array(
                        html_entity_decode($leprod->getAttribut("libelle"),ENT_QUOTES),
                        $prods[$i]->getAttribut("quantite"),
                        $leprod->getAttribut("ht"),
                        $mtnt_ht,
                        $tva,
                        $remise,
                        $ttc
                    );
                }
                else
                {
                    $data[]=array("","","","","","","");
                }
            }
        }
        else
        {
            // Header
            $header=array([115,"Description"],[35,"Montant TTC"],[35,"Remboursement"]);

            $headerWidth = 0;

            for($i=0;$i<count($header);$i++){
                $this->Cell($header[$i][0],7,$header[$i][1],1,0,"C",true);
                $headerWidth += $header[$i][0];
            }

            $this->ln();
            $this->setX(self::$margin_left);
            // Color and font restoration
            //$this->SetFillColor(224,235,255);
            $this->SetTextColor(0);
            $this->SetFont("DejaVuSans","",8);



            // Infos Produits
            $fill = false;
            $origine = 'Transféré de la Facture N°'.$laFacture->getAttribut('id').' du '.$laFacture->getAttribut('creation');
            $data= array([$origine,$ttc_origine,$a_rembourser],[$description,'',''],['','',''],[$raison,'','']);
        }
        
        foreach($data as $row)
        {
            for($i = 0; $i < count($row); $i++){
                if($i === 0)
                    $this->Cell($header[$i][0],6,$row[$i],"LR",0,"L",$fill);
                else
                    $this->Cell($header[$i][0],6,$row[$i],"LR",0,"R",$fill);
            }

            $this->Ln();
            $this->setX(self::$margin_left);
//            $fill = !$fill;
        }
        
        // Closing line
        $this->Cell($headerWidth,0,"","T");
        $this->Ln();
        
        if($title !== 'Avoir')
            return array($htCumule,$tvaCumule,$ttcCumule,$accompte);
        else
            return array($laFacture->getAttribut('ht'),$laFacture->getAttribut('tva'),$ttc_origine,$a_rembourser,$net_apayer);
    }
    
    
    
    
    private function foot(array $prix,string $type){
        
        
        if($this->GetY() > 232){
            $this->AddPage();
        }
        
        
        
        
        /* MENTIONS LÉGALES */
        
        $this->SetY(232);
        $this->SetFont("DejaVuSans", "", 7);
        $this->SetX(self::$margin_left-1);
        $this->Write(3, "Règlement de réception.");
        $this->Ln();
        
        $this->SetX(self::$margin_left-1);
        $this->Write(3, "Escompte pour règlement anticipé: 0%");
        $this->Ln();
        
        $this->SetX(self::$margin_left-1);
        $this->Write(3, "En cas de retard de paiement, une pénalité égale à 3 fois le taux d'intérêt légal sera exigible (Décret 2009-138 du 9 février 2009).");
        $this->Ln();
        
        $this->SetX(self::$margin_left-1);
        $this->Write(3, "Pour les professionnels, une indemnité minimum forfaitaire de 40 euros pour frais de recouvrement sera exigible (Décret 2012-1115 du 9 octobre 2012).");
        $this->Ln();
        
        $this->SetX(self::$margin_left-1);
        $this->SetFont("DejaVuSans", "B", 7);
        $this->Write(3, "RÉSERVE DE PROPRIÉTÉ ");
        $this->SetFont("DejaVuSans", "", 7);
        $this->Write(3, ": Bv informatique se réserve la propriété des matériels, fournitures et logiciels jusqu'au paiement intégral du prix par l'acheteur.");
        $this->Ln();
        $this->SetY($this->GetY()+2);
        
        
        
        
        /* TABLEAU NET A PAYER */
        
        $tableWidth=60;
        $this->SetX(self::$margin_right-$tableWidth);
        $this->Cell($tableWidth,0,"","T");
        $this->Ln();
        $this->SetFont("DejaVuSans", "", 9);
        if($type !== 'Avoir')
            $data=[["Total HT",$prix[0]],["Total TVA",$prix[1]],["Total TTC",$prix[2]],["Accomptes",$prix[3]],["Net À Payer",strval($prix[2])."€"],["Solde Dû",strval($prix[2]-$prix[3])."€"]];
        else
            $data=[["Total HT",$prix[0]],["Total TVA",$prix[1]],["Total TTC",$prix[2]],["À rembourser",$prix[3]],["Net À Payer",strval($prix[4])."€"]];
        
        for($i=0;$i < count($data); $i++){
            $this->SetX(self::$margin_right-$tableWidth);
            $this->Cell($tableWidth/2,6,$data[$i][0],"LR",0,"L",true);
            $this->Cell($tableWidth/2,6,$data[$i][1],"LR",0,"R",false);
            $this->Ln();
            if($i === 3){
                $this->SetX(self::$margin_right-$tableWidth);
                $this->Cell($tableWidth,0,"","T");
                $this->Ln();
            }
        }
        $this->Cell(self::$largeur-22,0,"","T"); // (margin)*2 - taille des bordures arrondi à l"unité supérieure
        $this->Ln();
        
        
        
        
        /* BAS DE PAGE */
        
        $mentionFooter="Siret : 81529163800014 - APE : 9511Z - N° TVA intracom : FR41815291638";
        
        // on veut être au milieu de l"écran, et avoir la moitié de message avant et l"autre moitié après le milieu
        $this->SetX(self::$largeur/2-strlen($mentionFooter)/2); 
        $this->Cell(strlen($mentionFooter),6,$mentionFooter,0,0,"C",false);
    }
    
    
    
    
    
    public function displayPDF() {
        $this->Output("I", "Factures.pdf");
    }
    
    
    
    
    
    
    
    
    /* CODE VENANT DE FPDF.ORG POUR PERMETTRE DE DONNER UN ANGLE AU TEXTE */    
    
    // url: http://www.fpdf.org/en/script/script2.php
    
    function Rotate($angle,$x=-1,$y=-1)
    {
        if($x==-1)
            $x=$this->x;
        if($y==-1)
            $y=$this->y;
        if($this->angle!=0)
            $this->_out('Q');
        $this->angle=$angle;
        if($angle!=0)
        {
            $angle*=M_PI/180;
            $c=cos($angle);
            $s=sin($angle);
            $cx=$x*$this->k;
            $cy=($this->h-$y)*$this->k;
            $this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
        }
    }
    
    
    
    function RotatedText($x,$y,$txt,$angle)
    {
        //Text rotated around its origin
        $this->Rotate($angle,$x,$y);
        $this->Text($x,$y,$txt);
        $this->Rotate(0);
    }
    
    
    
    
    
    
    
    /* CODE VENANT DE FPDF.ORG POUR PERMETTRE LA TRANSPARENCE DU TEXTE */
    
    // url: http://www.fpdf.org/en/script/script74.php
    
    protected $extgstates = array();

    // alpha: real value from 0 (transparent) to 1 (opaque)
    // bm:    blend mode, one of the following:
    //          Normal, Multiply, Screen, Overlay, Darken, Lighten, ColorDodge, ColorBurn,
    //          HardLight, SoftLight, Difference, Exclusion, Hue, Saturation, Color, Luminosity
    function SetAlpha($alpha, $bm='Normal')
    {
        // set alpha for stroking (CA) and non-stroking (ca) operations
        $gs = $this->AddExtGState(array('ca'=>$alpha, 'CA'=>$alpha, 'BM'=>'/'.$bm));
        $this->SetExtGState($gs);
    }

    function AddExtGState($parms)
    {
        $n = count($this->extgstates)+1;
        $this->extgstates[$n]['parms'] = $parms;
        return $n;
    }

    function SetExtGState($gs)
    {
        $this->_out(sprintf('/GS%d gs', $gs));
    }

    function _enddoc()
    {
        if(!empty($this->extgstates) && $this->PDFVersion<'1.4')
            $this->PDFVersion='1.4';
        parent::_enddoc();
    }

    function _putextgstates()
    {
        for ($i = 1; $i <= count($this->extgstates); $i++)
        {
            $this->_newobj();
            $this->extgstates[$i]['n'] = $this->n;
            $this->_put('<</Type /ExtGState');
            $parms = $this->extgstates[$i]['parms'];
            $this->_put(sprintf('/ca %.3F', $parms['ca']));
            $this->_put(sprintf('/CA %.3F', $parms['CA']));
            $this->_put('/BM '.$parms['BM']);
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    function _putresourcedict()
    {
        parent::_putresourcedict();
        $this->_put('/ExtGState <<');
        foreach($this->extgstates as $k=>$extgstate)
            $this->_put('/GS'.$k.' '.$extgstate['n'].' 0 R');
        $this->_put('>>');
    }

    function _putresources()
    {
        $this->_putextgstates();
        parent::_putresources();
    }
}

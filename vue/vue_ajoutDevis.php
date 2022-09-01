<!--
 * Description de vue_ajoutDevis.php
 *
 * auteur MASCLET Sylvain
 * Creation 11-02-2022
 * Derniere MAJ 25/02/2022
 * vue de l'ajout des devis
-->

<div class="carre carre_principal">
        
    <div class="titreTexte">
        <div class="titreArticle">
            <h3><?php echo $titre; ?></h3>
        </div>
        
        
        <form id="ajoutDev" action="./?action=ajoutdevis" method="POST"></form>
        
        
        
        <h4>Choix du client & des produits</h4>
        <table class="tableLeft">
            <tbody>
                <tr>
                    <td>
                        <!-- SELECT CLIENT -->

                        <h4>Client: </h4>    
                        <?php echo $clientSelect; ?>
                        <button class="btn btn_recherche" id="choixClient"><i id="checked" class="fa-solid fa-check"></i></button>



                        <!-- SELECT PRODUITS -->

                        <div class="centered">
                            <h4>Produit: </h4>
                            <?php echo $produitSelect; ?>
                            <button class="btn btn_recherche" id="addProduit"><i class="fa-solid fa-plus"></i></button>
                        </div>
                    </td>
                    <td class="containsATable">
                        <!-- TABLEAU DES PRODUITS ET NOM DU CLIENT -->

                        <table class="infos_facture">
                            <tr>
                                <th>Client</th>
                                <th>Produits</th>
                            </tr>
                            <tr>
                                <td>
                                    <p name="leClient"></p>
                                    <input form="ajoutDev" type="hidden" name="idClient" value="">
                                </td>
                                <td>
                                    <table id="lesProduits">
                                        <tr>
                                            <th>Nom du produit</th>
                                            <th>Suppression</th>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </tbody>
        </table>
        
        
        
        <br><br>
        
        
        
        
        <h4>Informations complémentaires</h4>
        <table class="tableLeft">
            <tbody>
                <tr>
                    <td>
                        <h4>État de la transaction: </h4>
                        <div>
                            <input form="ajoutDev" type="radio" id="encours" name="radioEtat" value="DE" checked>
                            <label for="encours">En cours</label>
                            <input form="ajoutDev" type="radio" id="valide" name="radioEtat" value="DV">
                            <label for="valide">Validé</label>
                        </div>
                    </td>
                    <td>
                        <!-- DATE DE CRÉATION -->

                        <h4>Date de création: </h4>
                        <input name="dateCreation" form="ajoutDev" type="date" value="<?php echo date('Y-m-d'); ?>" min="2022-01-01" max="2090-12-31">


                        <br><br>

                        <!-- DATE DE FIN -->

                        <h4>Date de fin de validité: </h4> 

                        <!-- on récupère comme date de base, 30 jours après aujourd'hui -->    
                        <?php 
                        $thirtydays = new DateTime(); 
                        $thirtydays->add(new DateInterval('P30D'));
                        $min = date('Y-m-d');
                        $max = $thirtydays->format('Y-m-d');
                        ?>
                        <input name="dateFin" form="ajoutDev" type="date" value="<?php echo $max; ?>" min="<?php echo $min; ?>" max="<?php echo $max; ?>">
                    </td>
                </tr>
            </tbody>
        </table>
        
        
        
        
        
        
        <br><br>
        <input form="ajoutDev" type="submit" name="confirmAjout" value="ajouter un Devis">
        <div class="validMess">
            <?php echo $validMess; ?>
        </div>
    </div>
</div>
<script>
    /* APPEL DES FONCTIONS JS */
    
    $(document).on('click','#addProduit',{form:'ajoutDev'},addProdAjout);
    $(document).on('click','#choixClient',choixDuClient);
    $(document).on('change','input[name="dateCreation"]',modifEcheance);
    
</script>
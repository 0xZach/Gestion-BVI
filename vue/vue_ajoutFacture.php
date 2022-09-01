<!--
 * Description de vue_ajoutFacture.php
 *
 * auteur MASCLET Sylvain
 * Creation 11-02-2022
 * Derniere MAJ 25/02/2022
 * vue de l'ajout des factures
-->

<div class="carre carre_principal">
        
    <div class="titreTexte">
        <div class="titreArticle">
            <h3><?php echo $titre; ?></h3>
        </div>
        <form id="ajoutFact" action="./?action=ajoutfacture" method="POST"></form>
        
        
        
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
                                    <input form="ajoutFact" type="hidden" name="idClient" value="">
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
                        <h4>Date de création: </h4><br><br>
                        <input name="dateCreation" form="ajoutFact" type="date" 
                               value="<?php echo date('Y-m-d'); ?>" min="2000-01-01" max="2090-12-31">
                    </td>
                    <td>
                        <h4>État de la transaction: </h4><br>
                        <div>
                            <input form="ajoutFact" type="radio" id="encours" name="radioEtat" value="FE" checked>
                            <label for="encours">En cours</label>
                            <input form="ajoutFact" type="radio" id="valide" name="radioEtat" value="FV">
                            <label for="valide">Validé</label>
                        </div><br>
                    </td>
                </tr>
                <tr>
                    <td>
                        <h4>Accompte: </h4><br>
                        <input form="ajoutFact" name="accompte" type="number" min="0" step="0.01" value="0">€
                    </td>
                    <td>
                        <h4>Moyen de paiement: </h4><br>
                        <input form="ajoutFact" name="moyen_paiement" type="text" placeholder="ex: carte/chèque/etc...">
                    </td>
                </tr>
            </tbody>
        </table>
        
        
        
        <br><br>      
        <input form="ajoutFact" type="submit" name="confirmAjout" value="ajouter une Facture">
        <div class="validMess">
            <?php echo $validMess; ?>
        </div>
    </div>
</div>
<script>
    /* APPEL DES FONCTIONS JS */
    
    $(document).on('click','#addProduit',{form:'ajoutFact'},addProdAjout);
    $(document).on('click','#choixClient',choixDuClient);
</script>
<!--
 * Description de vue_modifUneFacture.php
 *
 * auteur MASCLET Sylvain
 * Creation 16-02-2022
 * Derniere MAJ 02/03/2022
 * vue de la modification d'une facture
-->

<div class="carre carre_principal">
        
    <div class="titreTexte">
        <div class="titreArticle">
            <h3><?php echo $titre; ?></h3>
        </div>
        <form id="modifFact" action="./?action=lafacture" method="POST"></form>
        <form id="ajoutLigne" action="./?action=lafacture&ajoutLigne=<?php echo $laFacture->getAttribut('id'); ?>" method="POST"></form>
        
        <h4>Infos</h4>
        
        <table form="modifFact" class="tableLeft" id="laFacture">
            <thead>
                <tr>
                    <th>Numéro de facture</th>
                    <th>Date de création</th>
                    <th>nom du Client</th>
                    <th>Date échéance</th>
                    <th>Mode de règlement</th>
                    <?php if($laFacture->getAttribut('etat') === 'FE'){?>
                        <th>Supprimer la facture</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>                
                <tr>
                    <?php if($laFacture->getAttribut('etat') === 'FE'){?>
                        <td>
                            <input form="modifFact" type="hidden" name="idFacture" value="<?php echo $laFacture->getAttribut('id'); ?>">
                            <p><?php echo $laFacture->getAttribut('id'); ?></p>
                        </td>

                        <td><input form="modifFact" class="infos" name="dateCreation" type="date"
                                   value="<?php echo $laFacture->getAttribut('creation'); ?>"></td>

                        <td>
                            <?php echo $clientSelect; ?>
                        </td>

                        <td><input form="modifFact" class="infos" name="dateFin" type="date" 
                                   min="<?php echo $laFacture->getAttribut('creation'); ?>"
                                   value="<?php echo $laFacture->getAttribut('echeance'); ?>"
                                   max="<?php echo date('Y-m-d', strtotime($laFacture->getAttribut('creation') . '+ 15 days')); ?>"></td>

                        <td><input form="modifFact" class="infos" name="moyen_paiement" value="<?php echo $laFacture->getAttribut('moyen'); ?>"></td>

                        <td><?php echo $buttonSupprFacture; ?></td>
                    <?php }else{?>
                            <td>
                                <p><?php echo $laFacture->getAttribut('id'); ?></p>
                            </td>

                            <td><p><?php echo $laFacture->getAttribut('creation'); ?></p></td>

                            <td>
                                <p><?php echo $laFacture->getAttribut('client')->getAttribut('nom'); ?></p>
                            </td>

                            <td><p><?php echo $laFacture->getAttribut('echeance'); ?></p></td>

                            <td><p><?php echo $laFacture->getAttribut('moyen'); ?></p></td>
                    <?php }?>
                </tr>
            </tbody>
        </table>
        
        
        
        <br><br>
        
        
        
        <!-- ÉTAT DE VALIDITÉ -->
        
        <div>
            <?php if($laFacture->getAttribut('etat') === "FE"){ ?>
                <h4>État de validité</h4>
                <input form="modifFact" type="radio" id="encours" name="radioEtat" value="FE" checked >
                <label for="encours">En cours</label>
                <br>
                <input form="modifFact" type="radio" id="valide" name="radioEtat" value="FV">
                <label for="valide">Validé</label>
            <?php } ?>
        </div>
        
        
        
        <br><br>
        
        
        
        
        <h4>Tableau des produits</h4>
        
        <table id="lesProduits">
            <thead>
                <tr>
                    <th>Description</th>
                    <th>Qté</th>
                    <th>P. U. HT</th>
                    <th>Montant HT</th>
                    <th>TVA</th>
                    <th>% REM</th>
                    <th>Montant TTC</th>
                    <?php if($laFacture->getAttribut('etat') === 'FE'){?>
                    <th>Supprimer</th>
                    <?php }?>
                </tr>
            </thead>
            <tbody>
                
                
                <?php echo $tableProduits; ?>
                
                
                
                <?php if($laFacture->getAttribut('etat') === "FE"){ ?>
                <tr>
                    <td>
                        <button form="ajoutLigne" class="btn btn_recherche"><i class="fa-solid fa-plus"></i></button>
                    </td>
                </tr>
                <?php } ?>
                
            </tbody>
            
        </table>  
        
        
        
        <br><br>
        
        
        
        <!--Total TTC et accomptes--> 
                
        <h4>Total</h4>
        
        
        <table class="tableLeft">
            <tbody>
                <tr>
                    <td>Montant TTC</td>
                    <td><?php echo $laFacture->getAttribut('ttc'); ?></td>
                </tr>
                <tr>
                    <td>Montant Accompte</td>
                    <td><?php echo $laFacture->getAttribut('accompte'); ?></td>
                </tr>
                <tr>
                    <td>Montant Total</td>
                    <td><?php echo $laFacture->getAttribut('ttc')-$laFacture->getAttribut('accompte'); ?></td>
                </tr>
            </tbody>
        </table>
        
        
        
        <br><br>
        
        
        
        <h4>Options</h4>
        
        <table class="tableLeft">
            <thead>
                <tr>
                    <th>Export au format PDF</th>
                
                <?php if($laFacture->getAttribut('etat') === 'FE'){?>
                    <th>modifier la Facture</th>
                <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a  class="btn btn_recherche sort" 
                            href="./?action=pdffacture&laFacture=<?php echo $laFacture->getAttribut('id'); ?>" 
                            target="_blank" name="confirmExport">
                            <i class="fa-solid fa-arrow-up-from-bracket"></i>
                        </a>
                    </td>
                <?php if($laFacture->getAttribut('etat') === 'FE'){?>
                    <td>
                        <button form="modifFact" name="confirmModif" class="btn btn_recherche">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </td>
                </tr>
                <?php } ?>
                
            </tbody>
        </table>
        
        
        
        <br><br>
        
        
        
        <div>
            <h4>Faire un avoir de la facture</h4>
            
            <form action="./?action=lesavoirs&laFacture=<?php echo $laFacture->getAttribut('id'); ?>" method="POST">
                <table class="tableLeft">
                    <thead>
                        <tr>
                            <th>Montant à rembourser</th>
                            <th>Raison de l'avoir</th>
                            <th>Description</th>
                            <th>Confirmer</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>
                                
                                <input type='number' min="0" step="0.01" name="rembourser">
                            </td>
                            <td>    
                                
                                <input type='text' name="raison" placeholder="ex: erreur de montant, etc...">
                            </td>
                            <td>
                                
                                <input type='text' name="description">
                            </td>
                            <td>
                                
                                <button class="btn btn_recherche" name="confirmAvoir" ><i class="fa-solid fa-arrow-up-right-from-square"></i></button>
                            </td>
                        </tr>
                    
                    </tbody>
                </table>
                
                
                <br><br>
                
                
            </form>
        </div>
        
    </div>
</div>
<script>
    $(document).on('change','.selectFacture',nouvId);
    $(document).on('change','input[name="dateCreation"]',modifEcheance);
    $('.ttc').keyup(_.debounce(modifTTC , 10));
    $(document).on('change','.selectFacture',{action:'lafacture',id:'<?php echo $laFacture->getAttribut('id'); ?>'},modifProduit);
</script>
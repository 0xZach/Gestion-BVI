<!--
 * Description de vue_modifUnDevis.php
 *
 * auteur MASCLET Sylvain
 * Creation 18-02-2022
 * Derniere MAJ 02/03/2022
 * vue de la modification d'une devis
-->

<div class="carre carre_principal">
        
    <div class="titreTexte">
        <div class="titreArticle">
            <h3><?php echo $titre; ?></h3>
        </div>
        <form id="modifDevis" action="./?action=ledevis" method="POST"></form>
        <form id="ajoutLigne" action="./?action=ledevis&ajoutLigne=<?php echo $leDevis->getAttribut('id'); ?>" method="POST"></form>
        
        <h4>Infos</h4>
        
        <table form="modifDevis" class="tableLeft" id="leDevis">
            <thead>
                <tr>
                    <th>Numéro de devis</th>
                    <th>Date de création</th>
                    <th>Date de fin de validité</th>
                    <th>nom du Client</th>
                    <?php if($leDevis->getAttribut('etat') === 'DE'){?>
                    <th>Supprimer le devis</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>                
                <tr>
                    <?php if($leDevis->getAttribut('etat') === 'DE'){?>
                    <td>
                        <input form="modifDevis" type="hidden" name="idDevis" value="<?php echo $leDevis->getAttribut('id'); ?>">
                        <p><?php echo $leDevis->getAttribut('id'); ?></p>
                    </td>
                    
                    <td><input form="modifDevis" class="infos" name="dateCreation" type="date"
                               value="<?php echo $leDevis->getAttribut('creation'); ?>"></td>
                    
                    <td><input form="modifDevis" class="infos" name="dateFin" type="date" 
                               min="<?php echo $leDevis->getAttribut('creation'); ?>"
                               value="<?php echo $leDevis->getAttribut('fin'); ?>"
                               max="<?php echo date('Y-m-d', strtotime($leDevis->getAttribut('creation') . '+ 30 days')); ?>"></td>
                    
                    <td>
                        <?php echo $clientSelect; ?>
                    </td>
                    
                    <td><?php echo $boutonSuppression; ?></td>
                    <?php }else{?>
                            <td>
                                <p><?php echo $leDevis->getAttribut('id'); ?></p>
                            </td>

                            <td><p><?php echo $leDevis->getAttribut('creation'); ?></p></td>
                            
                            <td><p><?php echo $leDevis->getAttribut('fin'); ?></p></td>

                            <td>
                                <p><?php echo $leDevis->getAttribut('client')->getAttribut('nom'); ?></p>
                            </td>
                    <?php }?>
                </tr>
            </tbody>
        </table>
        
        
        
        <br><br>
        
        
        
        <!-- ÉTAT DE VALIDITÉ -->
        
        <div>
            <?php if($leDevis->getAttribut('etat') === "DE"){ ?>
            <h4>État de validité</h4>
            <input form="modifDevis" type="radio" id="encours" name="radioEtat" value="DE" checked >
            <label for="encours">En cours</label>
            <br>
            <input form="modifDevis" type="radio" id="valide" name="radioEtat" value="DV">
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
                    <?php if($leDevis->getAttribut('etat') === 'DE'){?>
                    <th>Retirer le produit</th>
                    <?php }?>
                </tr>
            </thead>
            <tbody>
                

                <?php echo $tableProduits; ?>
                
                
                
                <?php if($leDevis->getAttribut('etat') === "DE"){ ?>
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
                    <td><?php echo $leDevis->getAttribut('net_apayer'); ?></td>
                </tr>
            </tbody>
        </table>
        
        
        
        
        <br><br>
        
        
        
        <h4>Options</h4>
        
        <table class="tableLeft">
            <thead>
                <tr>
                    <th>Export au format PDF</th>
                    <th>transformer le devis en facture</th>
                    <?php if($leDevis->getAttribut('etat') === 'DE'){?>
                    <th>Modifier le devis</th>
                    <?php } ?>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <a  class="btn btn_nav" 
                            href="./?action=pdfdevis&leDevis=<?php echo $leDevis->getAttribut('id'); ?>" 
                            target="_blank" name="confirmExport">
                            <i class="fa-solid fa-arrow-up-from-bracket"></i>
                        </a>
                    </td>
                    <td>
                        <a  class="btn btn_recherche" 
                            href="./?action=lesfactures&ledevis=<?php echo $leDevis->getAttribut('id'); ?>" name="confirmTransform">
                            <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        </a>
                    </td>
                    <?php if($leDevis->getAttribut('etat') === 'DE'){?>
                    <td>
                        <button form="modifDevis" name="confirmModif" class="btn btn_recherche">
                            <i class="fa-solid fa-pen-to-square"></i>
                        </button>
                    </td>
                    <?php } ?>
                </tr>
                
            </tbody>
        </table>  
        
        
    </div>
</div>
<script>
    $(document).on('change','.lesSelects',nouvId);
    $(document).on('change','input[name="dateCreation"]',modifEcheance);
    $('.ttc').keyup(_.debounce(modifTTC , 10));
    $(document).on('change','.selectDevis',{action:'ledevis',id:'<?php echo $leDevis->getAttribut('id'); ?>'},modifProduit);
</script>
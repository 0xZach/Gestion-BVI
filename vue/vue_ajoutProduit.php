<!--
/*
 * Description de vue_ajoutProduit.php
 *
 * auteur MASCLET Sylvain
 * Creation 07-02-2022
 * Derniere MAJ 07/03/2022
 * vue de l'ajout d'un produit
 */
-->
<div class="carre carre_principal">
        
    <div class="titreTexte">
        <div class="titreArticle">
            <h3><?php echo $titre; ?></h3>
        </div>
        <div class="ajout">
        <form action="./?action=ajoutproduit" method="POST">
            
            
            <h4>Nom:</h4>
            <input name="libelle" type="text" size="35" placeholder="ex: Robert Dupont/BVInformatique/...">
            
            
            
            <table>
                <tr>
                    <th>
                        <h4>Prix TTC</h4>
                    </th>
                    <th>
                        <h4>Type de produit</h4>
                    </th>
                </tr>
                <tr>
                    <td rowspan="2">
                        <input name="prix" type="number" min="0" step="0.01" placeholder="ex: 10.95">
                    </td>
                    <td>
                        <div>
                        <input type="radio" id="bien" name="radioProduit" value="bien" checked>
                        <label for="bien">bien</label>
                        <input type="radio" id="service" name="radioProduit" value="service">
                        <label for="service">service</label>
                        </div>
                        <div>
                            
                        </div>
                        <br>
                    </td>
                </tr>
                <tr>
                    <td name="tauxHoraire">
                        <div id="valeurVariante">
                            <h4>Code barre:</h4><input name="code_barre" type="text" size="20" placeholder="ex: 2200450056">
                        </div>
                    </td>
                </tr>
            </table>
            
            
            <br><br>
            
            
            <input class="btn btn_recherche" type="submit" name="confirmAjout" value="Ajouter un produit">
        </form>
            <div class="errMess">
                <?php echo $errMess; ?>
            </div>
            <div class="validMess">
                <?php echo $validMess; ?>
            </div>
        </div>
    </div>
</div>
<script>
    /* APPEL DES FONCTIONS JS */
    
    $(document).on('change',"input[name='radioProduit']",radioProduit);
    
    
    // empêche les scans de codebarre d'activer les input de type submit
    $(":input").keypress(function(event){
    if (event.which == '10' || event.which == '13') {
            event.preventDefault();
        }
    });
</script>
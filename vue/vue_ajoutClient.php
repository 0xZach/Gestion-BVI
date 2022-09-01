<!--
/*
 * Description de vue_ajoutClient.php
 *
 * auteur MASCLET Sylvain
 * Creation 26-01-2022
 * Derniere MAJ 23/02/2022
 * vue de l'ajout des clients
 */
-->
<div class="carre carre_principal">
        
    <div class="titreTexte">
        <div class="titreArticle">
            <h3><?php echo $titre; ?></h3>
        </div>
        <div class="ajoutCli">
        <form action="./?action=ajoutclient" method="POST">
            <h4>nom, prénom ou raison sociale:</h4>  <input name="nom" type="text" size="35" placeholder="ex:Robert Dupont/BVInformatique/...">      
            <table>
                <tr>
                    <th>
                        Adresse
                    </th>
                    <th>
                        Moyens de communication
                    </th>
                </tr>
                <tr>
                    <td>
                        <h4>Code postal:</h4> <input name="codepostal" type="number" placeholder="ex: 24000">
                        <h4>Ville:</h4>  <input name="ville" type="text" placeholder="ex: Périgueux">
                        <h4>Adresse:</h4>  <input name="adresse" type="text" size="35" placeholder="ex: 2 Rue de l'exemple">
                    </td>
                    <td>
                        <h4>Fix:</h4>  <input name="fix" type="text" placeholder="ex: 05 55 55 55 55">
                        <h4>Tél.:</h4>  <input name="mobile" type="text" placeholder="ex: 06 66 66 66 66">
                        <h4>Adresse mail:</h4>  <input name="mel" type="text" size="35" placeholder="exemple@orange.fr">
                    </td>
                </tr>
            </table>
            <br><br>
            <input class="btn btn_recherche" type="submit" name="confirmAjout" value="Ajouter un client">
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
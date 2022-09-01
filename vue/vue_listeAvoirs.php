<!--
 * Description de vue_listeAvoirs.php
 *
 * auteur MASCLET Sylvain
 * Creation 08-03-2022
 * Derniere MAJ 08/03/2022
 * vue permettant l'affichage des Avoirs
 -->


<div class="carre carre_principal">
        
    <div class="titreTexte">
        <div class="titreArticle">
            <h3><?php echo $titre; ?></h3>
        </div>
        
        
        <form id="exportListe" target="_blank" action="./?action=pdffacture&export=liste&type=avoir" method="POST"></form>
        
        <form action="./?action=lesavoirs" method="POST">
            <input type="text" name="recherche"><button class="btn btn_recherche recherche"><i class="fa-solid fa-magnifying-glass"></i></button>
            
            
            <br><br>
            
            
            <table class="tableLeft">
                <thead>
                    <th colspan="2">
                        <h4>Intervalle de temps</h4>
                    </th>
                </thead>
                <tbody>
                    <td>
                        Du <input type="date" name="intervalDebut" value="<?php echo $interval[0]; ?>"> 
                        au <input type="date" name="intervalFin" value="<?php echo $interval[1]; ?>">
                    </td>
                    <td>
                        <select name="intervalDefini">
                            <option>Personnalisé</option>
                            <option>Aujourd hui</option>
                            <option>Hier</option>
                            <option>Mois courant</option>
                            <option>Mois dernier</option>
                            <option>Année courante</option>
                            <option>Année précédente</option>
                        </select>
                    </td>
                </tbody>
            </table>
        </form>
        
        
        <br><br>
        
        
        <table id="lesAvoirs">
            <thead>
                <tr>
                    <th>
                        À exporter 
                        <br>
                        <input id="checkAll" type='checkbox'>
                    </th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a></th><th>Numéro d'avoir</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Raison</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Facture d'origine</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Nom du client</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Montant TTC</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Montant à rembourser</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Description</th>
                    <th>Export pdf</th>
                </tr>
            </thead>
            <tbody>                
                <?php echo $tabRows; ?>
            </tbody>
        </table>
        
        
        
        
        <br><br>
        
        
        
        <input form="exportListe" type="submit" class="btn btn_recherche sort" name="confirmExport" value="Exporter la liste">
        
        
        
    </div>
</div>
<script>
    /* APPEL DES FONCTIONS JS */
    
    $(document).on('click','.sort',sort);
    $(document).ready(paginate(0,'lesAvoirs'));
    $(document).on('click','#checkAll',{tableau:'lesAvoirs'},ajoutExport);
    $(document).on('keyup','.infos',{action:'lesavoirs'},_.debounce(modifInfos,100)); 
    $(document).on('change','select[name="intervalDefini"]',intervalDate);
</script>
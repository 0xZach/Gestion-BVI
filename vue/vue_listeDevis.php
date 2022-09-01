<!--
 * Description de vue_listeDevis.php
 *
 * auteur MASCLET Sylvain
 * Creation 15-02-2022
 * Derniere MAJ 16/02/2022
 * vue permettant l'affichage des devis
 -->


<div class="carre carre_principal">
        
    <div class="titreTexte">
        <div class="titreArticle">
            <h3><?php echo $titre; ?></h3>
        </div>
        
        
        
        <form id="exportListe" target="_blank" action="./?action=pdfdevis&export=liste" method="POST"></form>
        
        
        
        <form action="./?action=lesdevis" method="POST">
            
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
        
        
        
        
        <table id="lesDevis">
            <thead>
                <tr>
                    <th>
                        À exporter 
                        <br>
                        <input id="checkAll" type='checkbox'>
                    </th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a></th><th>Numéro de devis</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Date de création</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Date de fin de validité</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Nom du client</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Net à payer</th>
                    <th>Supprimer</th>
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
    $(document).on('click','.sort',{tableau:'lesDevis'},sort);
    $(document).ready(paginate(0,'lesDevis'));
    $(document).on('click','#checkAll',{tableau:'lesDevis'},ajoutExport);
    $(document).on('change','select[name="intervalDefini"]',intervalDate);
</script>
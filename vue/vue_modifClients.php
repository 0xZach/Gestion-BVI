<!--
 * Description de vue_modifClients.php
 *
 * auteur MASCLET Sylvain
 * Creation 31-01-2022
 * Derniere MAJ 23/02/2022
 * vue permettant l'affichage et la modification des clients
 -->


<div class="carre carre_principal">
        
    <div class="titreTexte">
        
        
        <div class="titreArticle">
            <h3><?php echo $titre; ?></h3>
        </div>
        
        
        <form action="./?action=lesclients" method="POST">
            <input type="text" name="recherche"><button class="btn btn_recherche recherche"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
        
        
        <br><br>
        
        
        <table id="lesClients">
            <thead>
                <tr>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a><th>Nom</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a></th><th>Ville</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a></th><th>Code postal</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a></th><th>Adresse</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a></th><th>Numéro de tel fix</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a></th><th>Numéro de tel mobile</th>
                    <th><a class="btn btn_sort sort" href="#"><i class="fa-solid fa-angle-down"></i></a></th><th>Adresse mail</th>
                    <th>Supprimer</th>
                </tr>
            </thead>
            <tbody>                
                <?php echo $tabRows; ?>
            </tbody>
        </table>
        
        
    </div>
</div>
<script>
    /* APPEL DES FONCTIONS JS */
    
    // si la fonction n'a pas d'argument, ne pas l'appeler avec les parenthèses ex: sort au lieu de sort()
    $(document).on('click','.sort',sort); 

    // appel de la fonction permettant de d'afficher sous forme de tableau les différentes informations
    $(document).ready(paginate(0,'lesClients'));
    
    // underscore.js nous offre la fonction _.debounce qui permet de faire une action
    // dès qu'un timer choisi (ici 1000ms) arrive à 0
    $(document).on('keyup','.infos',{action:'lesclients'},_.debounce(modifInfos,200));
    
</script>
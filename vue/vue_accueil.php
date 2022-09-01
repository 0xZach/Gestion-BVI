<!--
 * Description de vue_accueil.php
 *
 * auteur MASCLET Sylvain
 * Creation 26-01-2022
 * Derniere MAJ 28/01/2022
 * vue de la page d'accueil
-->
<div class="carre carre_principal">
        
    <div class="titreTexte">
        <div class="titreArticle">
            <h3><?php echo $titre; ?></h3>
        </div>
        <h5>Liens utiles:</h5>  
        <ul>
            <li class="texte"><a href="https://calendar.google.com/calendar/u/0/r?pli=1" target="_blank">google agenda</a></li>
            <li class="texte"><a href="http://192.168.1.2:5000/#/signin" target="_blank">NAS bvi</a></li>
        </ul>
<!--        <form action="./?action=accueil" method="POST">
            <input type="submit"  name="importCSV" value="Importer des infos csv">
        </form>-->
    </div>
</div>

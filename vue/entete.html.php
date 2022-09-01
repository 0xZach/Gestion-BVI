<!--
 * Description de entete.html.php
 *
 * auteur MASCLET Sylvain
 * Creation 26-01-2022
 * Derniere MAJ 20/05/2022
 * entete du site web
-->
<!DOCTYPE html>
<html lang="fr"> 
    <head>
        <title><?php echo $titre; ?></title>
        
        <meta http-equiv='cache-control' content='no-cache'>
        <meta http-equiv='expires' content='0'>
        <meta http-equiv='pragma' content='no-cache'>
        <meta charset="utf-8">
        
        <link rel="stylesheet" href="css/styles.css">
        <link rel="stylesheet" href="css/fontawesome-free-6.0.0-web/css/all.css">
        
        <script src="vue/jquery-3.6.0.min.js"></script>
        <script src="vue/underscore-min.js"></script>
        <script type="text/javascript" src="javascript/scripts.inc.js"></script>
    </head>
    <body>
        <header>
            <h1 class="titreSite">Gestion BVI</h1>
            <nav class="menu_header">
                <ul class="menu">
                    <?php if (isset($_SESSION['mdpOK'])) { ?>
                        <li class="btn btn_nav deconnexion"><a class="a_btn" href="./?action=off">Deconnexion</a></li>
                    <?php } ?>
                </ul>
            </nav>
        </header>

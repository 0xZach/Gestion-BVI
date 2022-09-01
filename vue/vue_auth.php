<!--
 * Description de vue_auth.php
 *
 * auteur MASCLET Sylvain
 * Creation 26-01-2022
 * Derniere MAJ 28/01/2022
 * vue de l'authentification
-->

<div class="carre carre_connexion">
    <div class="titre">
        <h2><i class="fas fa-sign-in-alt"></i>      Connexion :</h2>
    </div>
    <hr/>
    
    <form method="POST" action="./?action=auth">
        <input type="password" name="mdp" placeholder="mot de passe">
        <input type="submit" name="confirmAuth" value="s'authentifier">
    </form>
    <div class="errMess">
        <?php echo $errMess; ?>
    </div>
</div>


    <div class="background-panel">
        <div class="login-panel">
            <h1>Connexion au compte</h1>
           <?php if (isset($_SESSION['erreur'])): ?>
                <div class="erreur"><?php echo htmlspecialchars($_SESSION['erreur']); ?></div>
                <?php unset($_SESSION['erreur']);  ?>
            <?php endif; ?>

            <form action="routeur.php?controleur=membre&action=traiterConnexion" method="POST">
                <label for="loginOrEmail">ID/Adresse email :</label>
                <input type="text" id="loginOrEmail" name="loginOrEmail" placeholder="Entrez votre ID ou email" required>

                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" placeholder="Entrez votre mot de passe" required>

                <button type="submit">CONNECTER</button>
            </form>

            <p>Vous n'avez pas encore de compte ? <a href="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireInscription">S'INSCRIRE</a></p>
        </div>
    </div>


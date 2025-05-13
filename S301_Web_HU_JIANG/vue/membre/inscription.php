<div class="back-button">
    <button onclick="window.history.back()">
        <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/retour.png" alt="Retour" class="icon-arrow">
    </button>
</div>

<div class="container">
    <h1>Créer un compte</h1>
    <?php if (isset($_SESSION['erreur'])): ?>
    <div class="error-message">
        <?= htmlspecialchars($_SESSION['erreur']) ?>
    </div>
    <?php unset($_SESSION['erreur']); endif; ?>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="success-message">
            <?= htmlspecialchars($_SESSION['success']) ?>
        </div>
    <?php unset($_SESSION['success']); endif; ?>
	<?php if (isset($_GET['idGroupe'])){
		$idGroupe = $_GET['idGroupe'];
		$routeur = 'traiterInscriptionInvitation&idGroupe='.$idGroupe;
		}else{
			$routeur = 'traiterInscription';
		}
	?>
			
    <form action="routeur.php?controleur=membre&action=<?php echo $routeur;?>" method="POST">
        <div class="form-row">
            <div>
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div>
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
        </div>
        <label for="email">Adresse email :</label>
        <input type="email" id="email" name="email" required>
        
        <label for="telephone">Numéro de téléphone :</label>
        <input type="text" id="telephone" name="tel" required>

        <label for="adresse">Adresse :</label>
        <input type="text" id="adresse" name="adresse" required>
        
        <label for="dateNaissance">Date de naissance :</label>
        <input type="date" id="dateNaissance" name="dateNaissance" required>
        
        <label for="motdepasse">Mot de passe :</label>
<input type="password" id="motPasse" name="motPasse" required>

        
        <label for="confirmer_motdepasse">Confirmer le mot de passe :</label>
        <input type="password" id="confirmer_motdepasse" name="confirmer_motdepasse" required>
        
        <button type="submit" class="button">Valider</button>
    </form>
</div>



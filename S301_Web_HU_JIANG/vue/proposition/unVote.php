
<?php
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $idProp = $_GET['id']; 
} else {
    echo "ID de la proposition manquant.";
    exit;
}

if (isset($_GET['idMembre']) && is_numeric($_GET['idMembre'])) {
    $idMembre = $_GET['idMembre'];  
} else {
    echo "ID de membre manquant.";
    exit;
}
?>

<div class="back-button">
<a href="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=proposition&action=lireProposition&id=<?php echo htmlspecialchars($idProp); ?>&idMembre=<?php echo htmlspecialchars($idMembre); ?>">
    <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/retour.png" alt="Retour" class="icon-arrow">
</a>

    </a>
</div>


<h1>Créer un vote</h1>

<form method="POST">
    <label for="dureeLimiteeVote">Durée du vote (en heures) :</label>
    <input type="number" id="dureeLimiteeVote" name="dureeLimiteeVote" value="24" required>
    <br>

    <label for="modeVote">Mode de vote :</label>
    <select id="modeVote" name="modeVote">
        <option value="majorite" selected>Majorité simple</option>
      
    </select>
    <br>


    <button type="submit">Créer le vote</button>
</form>

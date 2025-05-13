<div class="profil-container">

<div class="back-button">
    <button onclick="window.location.href = 'routeur.php?controleur=discussion&action=lireDiscussion&id=<?php echo urlencode($idMembre); ?>';">
        <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/retour.png" alt="Retour" class="icon-arrow">
    </button>
</div>

    <h1>Profil de <?php echo $membre->getNomMembre() . ' ' . $membre->getPrenomMembre(); ?></h1>
    <img src="<?php echo !empty($membre->getPhoto()) ? './source/photoprofil/' . $membre->getPhoto() : './source/photoprofil.png'; ?>" alt="Photo de profil" class="photo-profil">

    <ul class="profil-details">
        <li><strong>Login :</strong> <?php echo $membre->getLoginMembre(); ?></li>
        <li><strong>Nom :</strong> <?php echo $membre->getNomMembre(); ?></li>
        <li><strong>Prénom :</strong> <?php echo $membre->getPrenomMembre(); ?></li>
        <li><strong>Email :</strong> <?php echo $membre->getEmailMembre(); ?></li>
        <li><strong>Date de naissance :</strong> <?php echo $membre->getDateNaissanceMembre(); ?></li>
        <li><strong>Adresse :</strong> <?php echo $membre->getAdresseMembre(); ?></li>
        <li><strong>Numéro de téléphone :</strong> <?php echo $membre->getTelMembre(); ?></li>
        <li><strong>Date d'inscription :</strong> <?php echo $membre->getDateInscriptionMembre(); ?></li>
    </ul>
</div>
    <?php

if (isset($_COOKIE['user_id']) && $_COOKIE['user_id'] == $membre->getIdMembre()) {
    echo '<a href="routeur.php?controleur=membre&action=afficherFormulaireModification&id=' . $membre->getIdMembre() . '" class="btnModifierProfil">Modifier mon profil</a>';
    echo '<a href="routeur.php?controleur=membre&action=deconnecter" class="btnDeconnecter">Se déconnecter</a>';}




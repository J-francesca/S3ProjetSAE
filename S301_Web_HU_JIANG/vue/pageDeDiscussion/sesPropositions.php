

<?php


foreach ($tab_p as $p) {
    ?>
    <div class="proposition" id="<?php echo $p->getIdProp(); ?>">
        <div class="Partie1">
		
			<?php 
			$idMembre = $p->getIdMembre();;
			$m = Membre::getMembreById($idMembre);
			$image = $m->getPhoto(); 
			?>
			<div id="avatar-container">
				<img src="./source/photoprofil/<?php echo $image; ?>" alt="ImageGroupe" id="Image-groupe">
			</div>
            <!-- <img src="./source/photoprofil.png" alt="photo" id="photoPropo"> -->
			  

			<p> <?php echo $m->getNomMembre();?> <?php echo $m->getPrenomMembre();?> </p>

        </div>
        <div class="Partie2">
            <p><strong><?php echo $p->getTitreProp(); ?> </strong></p>
            <p><?php echo $p->getDescriptProp(); ?></p>
        </div>
        <div class="lien-detail">
        <a href="routeur.php?controleur=proposition&action=lireProposition&id=<?php echo $p->getIdProp(); ?>&idMembre=<?php echo $id; ?>&idGroupe=<?php echo $idGroupe; ?>">Lire la proposition</a>
        </div>
    </div>
    <?php
}
?>


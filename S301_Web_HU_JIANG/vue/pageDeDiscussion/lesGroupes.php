<?php

// Supposez que $tab_groupe est un tableau d'objets 'Groupe'
foreach ($tab_groupe as $g) {
	$image = $g->getImageGroupe(); 
    ?>
	<a href="routeur.php?controleur=discussion&action=lireDiscussionAvecPropositions&id=<?php echo $id; ?>&idGroupe=<?php echo $g->getIdGroupe(); ?>">
		<div class="groupe" id = "<?php echo $g->getIdGroupe(); ?>" > 
			<div id="avatar-container">
				<img src="./source/avatarGroupe/<?php echo $image; ?>" alt="ImageGroupe" alt="ImageGroupe" id="Image-groupe">
			</div>
			<p><?php echo $g->getNomGroupe(); ?></p>
		</div>
	</a>
    <?php
}

?>

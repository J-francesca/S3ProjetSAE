<?php

$tab_m;

foreach ($tab_m as $m){
	?>
	<a href="routeur.php?controleur=membre&action=afficherMembreProfil&id=<?php echo $m->getIdMembre();?>">
		<?php 
			$unMembre = Membre::getMembreById($m->getIdMembre());
			$idMembre = $unMembre ->getIdMembre();
			
			$membre = Membre::getMembreById($idMembre);
			$image = $membre->getPhoto(); 
			?>
			<div id="avatar">
				<img src="./source/photoprofil/<?php echo $image; ?>" alt="ImageGroupe" id="Image-groupe">
			</div>
    </a>

	<?php
}


?>
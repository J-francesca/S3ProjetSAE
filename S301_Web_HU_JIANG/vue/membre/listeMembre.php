		<div class="listeMembre">
			<h1>Liste des membres du groupe</h1>
			<p>Cocher une ou des cases pour retirer un ou des membres dans ce groupe</p>
			<form action="routeur.php?controleur=membre&action=retirerMembre&idMembre=<?php echo $id; ?>&idGroupe=<?php echo $idGroupe; ?>" method="POST">
				<div class="liste">
				<?php

					foreach ($tab_m as $m){
						?>
						<?php 
							$idM = $m ->getIdMembre(); 
							$membre = Membre::getMembreById($idM);
							if ($membre ->getIdMembre() != $id){
						?>
						<div class="composant">
							<img src="./source/photoprofil.png" alt="PhotoProfil" id="Photo-profil">
							<label for="membre"><?php echo $membre ->getNomMembre(); ?> <?php echo $membre ->getPrenomMembre(); ?></label><br>
							<input type="checkbox" name="membres[]" value="<?php echo $membre ->getIdMembre(); ?>" id="<?php echo $membre ->getIdMembre(); ?>">
						</div>
						<?php
							}
					}
				?>
				</div>
			
				<div class="element2">
					<button type="submit">Supprimer</button>
				</div>
			
			</form>
		</div>
		<a href="routeur.php?controleur=parametre&action=lireParametreGroupe&id=<?php echo $id; ?>&idGroupe=<?php echo $idGroupe; ?>&parametre=gestionGroupe&classe=groupe&ext=html">
			<img src="./source/retour.png" alt="Retour" id="Retour">
		</a>
		<div class="membre">
			
			<h1>Autres membres du groupe</h1>
			<div>
				<div class="liste">
				<?php

					foreach ($tab_m as $m){
						?>
						<?php 
							$idM = $m ->getIdMembre(); 
							$membre = Membre::getMembreById($idM);
							$role = Participation::getRoleByIds($membre ->getIdMembre(), $idGroupe);
							
							if ($role == 7){
						?>
						<div class="composant">
							<img src="./source/photoprofil.png" alt="PhotoProfil" id="Photo-profil">
							<label for="membre"><?php echo $membre ->getNomMembre(); ?> <?php echo $membre ->getPrenomMembre(); ?></label><br>
						</div>
						<?php
							}
					}
				?>
				</div>
			</div>

		</div>
		


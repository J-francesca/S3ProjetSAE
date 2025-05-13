		<a href="routeur.php?controleur=parametre&action=lireParametreGroupe&id=<?php echo $id; ?>&idGroupe=<?php echo $idGroupe; ?>&parametre=gestionGroupe&classe=groupe&ext=html">
			<img src="./source/retour.png" alt="Retour" id="Retour">
		</a>
		<div class="assesseur">
			
			<h1>Assesseurs du groupe</h1>
			
			<form action="routeur.php?controleur=role&action=retirerRole&idMembre=<?php echo $id; ?>&idGroupe=<?php echo $idGroupe; ?>" method="POST">
				<div class="liste">
				<?php

					foreach ($tab_m as $m){
						?>
						<?php 
							$idM = $m ->getIdMembre(); 
							$membre = Membre::getMembreById($idM);
							$role = Participation::getRoleByIds($membre ->getIdMembre(), $idGroupe);
							
							if ($role == 5){
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
					<button type="submit">Retirer</button>
				</div>
			
			</form>
			<form action="routeur.php?controleur=role&action=attribuerRole&idMembre=<?php echo $id; ?>&idGroupe=<?php echo $idGroupe; ?>&idRole=2" method="POST">
					<div class="element3">
						<div class="composant">
							<p>Nom:</p>
							<input type="text" id="nom" name="nom" required>
						</div>
						<div class="composant">
							<p>Prénom:</p>
							<input type="text" id="prenom" name="prenom" required>
						</div>
					</div>
					<div class="element2">
						<button type="submit">Ajouter</button>
					</div>
				</form>
		</div>
		


		<div class="listeTheme">
			<h1>Liste des thèmes du groupe</h1>
			<p>Cocher une ou des cases pour supprimer un ou des thèmes dans ce groupe</p>
			<form action="routeur.php?controleur=groupeTheme&action=supprimerThemes&idMembre=<?php echo $id; ?>&idGroupe=<?php echo $idGroupe; ?>" method="POST">
				<div class="liste">
				<?php
					$tab_gt = Groupe_Theme::getGroupeThemeByIdGroupe($idGroupe);
					foreach ($tab_gt as $gt){
						$idTheme = $gt->getIdTheme();
						$theme = Theme::getThemeById($idTheme);
						?>
						<div class="composant">
							<label for="theme"><?php echo $theme->getNomTheme(); ?></label><br>
							<input type="checkbox" name="themes[]" value="<?php echo $theme ->getIdTheme(); ?>" id="<?php echo $theme ->getIdTheme(); ?>">
						</div>
					<?php
					}
					
				?>
					</div>
					<div class="element2">
						<button type="submit">Supprimer</button>
					</div>
				</form>
				<form action="routeur.php?controleur=groupeTheme&action=ajouterTheme&idMembre=<?php echo $id; ?>&idGroupe=<?php echo $idGroupe; ?>" method="POST">
					<div class="element3">
						<div class="composant">
							<p>Nouveau thème:</p>
							<input type="text" id="nomTheme" name="nomTheme" required>
						</div>
						<div class="composant">
							<p>Budget concerné:</p>
							<input type="number" id="budget" name="budget" min ="0" required>
						</div>
					</div>
					<div class="element2">
						<button type="submit">Ajouter</button>
					</div>
				</form>
		</div>
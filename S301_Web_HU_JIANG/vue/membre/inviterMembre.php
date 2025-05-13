		<div class="inviterMembre">
			<h1>Inviter un membre à joindre dans le groupe</h1>
			<p>Envoyer un lien d'invitation pour inviter l'utilisateur à rejoindre le groupe.</p>
			
			<form action="routeur.php?controleur=invitation&action=envoyerInvitation&idMembre=<?php echo $id; ?>&idGroupe=<?php echo $idGroupe; ?>" method="POST">

				<div class="element1">
					<p>Email:</p>
					<input type="email" id="email" name="email" required>
				</div>
				<div class="element2">
					<button type="submit">Envoyer</button>
				</div>
			
			</form>
		</div>
<?php

require_once("./modele/membre.php");
require_once("./modele/role.php");
require_once("./modele/participation.php");
require_once("./modele/groupe.php");
	
class ControleurRole{
	
	public static function retirerRole() {
		
		$idM = $_GET['idMembre'];
		$idGroupe = $_GET['idGroupe'];

		// Vérifier si des membres ont été sélectionnés 
		if (isset($_POST['membres']) && count($_POST['membres']) > 0) {
			$roleARetirer = $_POST['membres']; 

			try { 
				
				$stmtParticipation = connexion::pdo()->prepare(
				"UPDATE Participation 
				SET IdRole = 7
				WHERE IdMembre = ? 
				AND IdGroupe = ?");
							
				foreach ($roleARetirer as $idMembre) {
					
					$stmtParticipation->execute([$idMembre, $idGroupe]);
				
				}

				// Message de succès
				$_SESSION['message'] = "Le rôle du membre ou des membres sélectionnés a été retiré.";
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit();

			} catch (Exception $e) {
				$_SESSION['message'] = "Une erreur est survenue lors de la suppression des membres : " . $e->getMessage();
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit();
			}

		} else {
			// Aucun membre sélectionné
			$_SESSION['message'] = "Aucun membre n'a été sélectionné pour la modification.";
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			exit();
		}
	}
	
	public static function attribuerRole() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$nomMembre = $_POST['nom'];
			$prenomMembre = $_POST['prenom'];
			$idGroupe = $_GET['idGroupe'];
			$idM = $_GET['idMembre'];
			$roleAttribuer = (int)$_GET['idRole'];
			try {
				$nomMembreFormatte = ucfirst(strtolower($nomMembre));
				$prenomMembreFormatte = ucfirst(strtolower($prenomMembre));
				$lesMembres = Membre::getAllMembres();
				
				if($roleAttribuer !== 3){
					
					$nombreMembresAvecRole = Participation::compterMembresParRole($idGroupe, $roleAttribuer);
					if ($nombreMembresAvecRole >= 7) {
						$_SESSION['message'] = "Le rôle est déjà attribué à 7 membres dans ce groupe.";
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit();
					}
				}
				
				foreach($lesMembres as $membre){
				
					$verif = Participation::verifierMembreGroupe($membre->getIdMembre(), $idGroupe);
					if($membre->getNomMembre() == $nomMembreFormatte && $membre->getPrenomMembre() == $prenomMembreFormatte && $verif==1){
						
						if(Participation::getRoleByIds($membre->getIdMembre(), $idGroupe) == $roleAttribuer){
							$_SESSION['message'] = "Ce membre possède déjà le rôle.";
							header('Location: ' . $_SERVER['HTTP_REFERER']);
							exit();
						}
						
						Participation::modifierRole($membre->getIdMembre(), $idGroupe, $roleAttribuer);
						$_SESSION['message'] = "L'attribution du rôle avec succès.";
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit();
					
					}
				}
				$_SESSION['message'] = "Cet utilisateur n'appartient pas dans ce groupe. Veuillez verifier le membre saisi.".$verif;
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit();
			} catch (Exception $e) {
				$_SESSION['message'] = "Une erreur est survenue lors de l'attibution de rôle. Veuillez réessayer.";
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit();
			}
			
		}
		
	}
	
	
}


?>

<?php


require_once("./modele/membre.php");
require_once("./modele/theme.php");
require_once("./modele/groupe.php");
require_once("./modele/groupetheme.php");
require_once("./modele/proposition.php");

	
class ControleurGroupeTheme{
	
	public static function ajouterTheme() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$nomTheme = $_POST['nomTheme'];
			$budget = $_POST['budget'];
			$idGroupe = $_GET['idGroupe'];
			$idM = $_GET['idMembre'];
			try {
				$nomThemeFormatte = ucfirst(strtolower($nomTheme));
				$lesThemes = Theme::getAllThemes() ;
				foreach($lesThemes as $theme){
					if ($theme->getNomTheme() === $nomThemeFormatte) {
						$idTheme = $theme->getIdTheme();
						$verif = Groupe_Theme::getGroupeThemeByIds($idGroupe, $idTheme);
						if($verif !== false){
							$_SESSION['message'] = "Le thèmea existe déjà.";
							header("Location: routeur.php?controleur=parametre&action=lireParametreGroupe&id=$idM&idGroupe=$idGroupe&parametre=themesGroupe&classe=groupeTheme&ext=php");
							exit();
						}else{
							Groupe_Theme::ajouterGroupeTheme($idGroupe, $idTheme, $budget);
							$_SESSION['message'] = "Le thème a été ajouté avec succes.";
							header("Location: routeur.php?controleur=parametre&action=lireParametreGroupe&id=$idM&idGroupe=$idGroupe&parametre=themesGroupe&classe=groupeTheme&ext=php");
							exit();
						}
					}
				}
				Theme::ajouterTheme($nomThemeFormatte);
				$idTheme = Theme::getIdThemeByNom($nomThemeFormatte);
				Groupe_Theme::ajouterGroupeTheme($idGroupe, $idTheme, $budget);
				$_SESSION['message'] = "Le thème a été ajouté avec succes.";
				header("Location: routeur.php?controleur=parametre&action=lireParametreGroupe&id=$idM&idGroupe=$idGroupe&parametre=themesGroupe&classe=groupeTheme&ext=php");
			} catch (Exception $e) {
				$_SESSION['erreur'] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
				header('Location: ' . $_SERVER['HTTP_REFERER']);
			}
			exit();
		}
		
	}
	
	public static function supprimerThemes() {
		$idM = $_GET['idMembre'];
		$idGroupe = $_GET['idGroupe'];

		if (isset($_POST['themes']) && count($_POST['themes']) > 0) {
			$themesASupprimer = $_POST['themes']; 

			try { 

				$stmt = connexion::pdo()->prepare("DELETE 
				FROM Groupe_Theme 
				WHERE IdTheme = ?
				AND IdGroupe = ? ");
				
				foreach ($themesASupprimer as $idTheme) {
					$propositionASupprimer = Proposition::getPropositionByIdTheme($idTheme);
					foreach ($propositionASupprimer as $idProp){
					
						Groupe_Theme::supprimerPropositionTheme($idProp);
					
					}
					
					$stmt->execute([$idTheme, $idGroupe]);
				
				}

		
				$_SESSION['message'] = "Le thème ou les thèmes sélectionnés ont été supprimés.";
				header("Location: routeur.php?controleur=parametre&action=lireParametreGroupe&id=$idM&idGroupe=$idGroupe&parametre=themesGroupe&classe=groupeTheme&ext=php");
				exit();

			} catch (Exception $e) {
				// Gestion des erreurs
				$_SESSION['message'] = "Une erreur est survenue lors de la suppression des membres : " . $e->getMessage();
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit();
			}

		} else {
			
			$_SESSION['message'] = "Aucun thème n'a été sélectionné pour la suppression.";
			header("Location: routeur.php?controleur=parametre&action=lireParametreGroupe&id=$idM&idGroupe=$idGroupe&parametre=themesGroupe&classe=groupeTheme&ext=php");
			exit();
		}
		
	}
	
	
}


?>

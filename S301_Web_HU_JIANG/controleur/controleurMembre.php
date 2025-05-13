<?php

require_once("./modele/membre.php");
require_once("./modele/participation.php");

class ControleurMembre {


	public static function afficherWelcome()
	{

		$titre = "Page d'accueil";
		$Style = "StyleBienvenue";
		include("./vue/debut.php");
		include("./vue/membre/welcome.php");
		include("./vue/fin.html");
	}



	public static function afficherFormulaireConnexion($erreur = null)
	{
		$titre = "Connexion";
		$Style = "StyleLogin";
		include("./vue/debut.php");
		include("./vue/membre/connexion.php");
		include("./vue/fin.html");
	}

	public static function traiterConnexion()
	{

		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$loginOrEmail = $_POST['loginOrEmail']; 
			$password = $_POST['password'];  

			$membre = Membre::verifierIdentifiants($loginOrEmail, $password);
			$idMembre = Membre::getIdMembreByLoginOrEmailAndPassword($loginOrEmail, $password);

			var_dump($membre); 
			var_dump($idMembre); 
			

			if ($membre) {
				$expireTime = time() + (3 * 3600); 
				setcookie("user_id", $idMembre, $expireTime, "/");
				$_SESSION['user_id'] = $idMembre;
				$_SESSION['expire_time'] = $expireTime;
				header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=discussion&action=lireDiscussion&id=' . $idMembre);
				exit();  
			} else {
				$_SESSION['erreur'] = "Identifiants ou mot de passe incorrects. Veuillez réessayer.";
				header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireConnexion');
				exit();
			}
		}
	}

	





	public static function afficherFormulaireInscription($erreur = null) {

	
		$titre = "Inscription";
		$Style = "StyleInscrit";
		include("./vue/debut.php");
		include("./vue/membre/inscription.php");
		include("./vue/fin.html");
	}
	

	public static function traiterInscription() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$nom = $_POST['nom'];
			$prenom = $_POST['prenom'];
			$adresse = $_POST['adresse'];
			$email = $_POST['email'];
			$tel = $_POST['tel'];
			$dateNaissance = $_POST['dateNaissance'];
			$motPasse = isset($_POST['motPasse']) ? $_POST['motPasse'] : null;
			$confirmerMotPasse = $_POST['confirmer_motdepasse'];
	
			$nom = ucfirst(strtolower($nom)); 
			$prenom = ucfirst(strtolower($prenom));
			$login = Membre::genererLogin($prenom, $nom);
	
			if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
				$_SESSION['erreur'] = "Format d'email invalide.";
				header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription');
				exit();
			}
	
			if (Membre::verifierEmailExist($email)) {
				$_SESSION['erreur'] = "L'email est déjà utilisé.";
				header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription');
				exit();
			}
	
			if (empty($motPasse)) {
				$_SESSION['erreur'] = "Le mot de passe ne peut pas être vide.";
				header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription');
				exit();
			}
	
			if ($motPasse !== $confirmerMotPasse) {
				$_SESSION['erreur'] = "Les mots de passe ne correspondent pas.";
				header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription');
				exit();
			}
	
			try {
				$to = $email;
				$subject = "Confirmation de vos informations d'inscription";
				$message = "Bonjour $prenom $nom,\n\n";
				$message .= "Voici un récapitulatif de vos informations d'inscription :\n\n";
				$message .= "Nom : $nom\n";
				$message .= "Prénom : $prenom\n";
				$message .= "Login : $login\n"; 
				$message .= "Adresse : $adresse\n";
				$message .= "Email : $email\n";
				$message .= "Téléphone : $tel\n";
				$message .= "Date de naissance : $dateNaissance\n\n";
				$message .= "Pour vous connecter, veuillez cliquer sur ce lien :\n";
				$message .= "https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireConnexion\n\n";
				$message .= "Utilisez votre login ou email pour vous connecter.\n\n";
				$message .= "Cordialement,\nL'équipe du site";
	
				$headers = 'From: ne-pas-repondre@projetshujiang.com' . "\r\n" .
						  'Reply-To: ne-pas-repondre@projetshujiang.com' . "\r\n" .
						  'X-Mailer: PHP/' . phpversion();
	
				if(mail($to, $subject, $message, $headers)) {
					Membre::ajouterMembre($nom, $prenom, $adresse, $email, $tel, $dateNaissance, $motPasse);
					$_SESSION['success'] = "Inscription réussie ! Un email récapitulatif vous a été envoyé.";
					header('Location: routeur.php?controleur=membre&action=afficherFormulaireConnexion');
				} else {
					throw new Exception("Erreur lors de l'envoi de l'email");
				}
			} catch (Exception $e) {
				$_SESSION['erreur'] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
				header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription');
			}
			exit();
		}
	}
	
	public static function traiterInscriptionInvitation() {
		if ($_SERVER['REQUEST_METHOD'] != 'POST') {
			return;
		}
	
		$idGroupe = isset($_GET['idGroupe']) ? $_GET['idGroupe'] : null;
		
		if (!$idGroupe) {
			$_SESSION['erreur'] = "ID de groupe manquant";
			header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription');
			exit();
		}
	
		$nom = trim($_POST['nom']);
		$prenom = trim($_POST['prenom']);
		$adresse = trim($_POST['adresse']);
		$email = trim($_POST['email']);
		$tel = trim($_POST['tel']);
		$dateNaissance = trim($_POST['dateNaissance']);
		$motPasse = isset($_POST['motPasse']) ? $_POST['motPasse'] : '';
		$confirmerMotPasse = isset($_POST['confirmer_motdepasse']) ? $_POST['confirmer_motdepasse'] : '';
		$login = Membre::genererLogin($prenom, $nom);  
	
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$_SESSION['erreur'] = "Format d'email invalide.";
			header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription&idGroupe=' . $idGroupe);
			exit();
		}
	
		if (Membre::verifierEmailExist($email)) {
			$_SESSION['erreur'] = "L'email est déjà utilisé.";
			header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription&idGroupe=' . $idGroupe);
			exit();
		}
	
		if (empty($nom) || empty($prenom) || empty($email) || empty($motPasse)) {
			$_SESSION['erreur'] = "Tous les champs obligatoires doivent être remplis.";
			header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription&idGroupe=' . $idGroupe);
			exit();
		}
	
		if ($motPasse !== $confirmerMotPasse) {
			$_SESSION['erreur'] = "Les mots de passe ne correspondent pas.";
			header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription&idGroupe=' . $idGroupe);
			exit();
		}
	
		try {
			connexion::pdo()->beginTransaction();
	
			$to = $email;
			$subject = "Confirmation de vos informations d'inscription";
			$message = "Bonjour $prenom $nom,\n\n";
			$message .= "Voici un récapitulatif de vos informations d'inscription :\n\n";
			$message .= "Nom : $nom\n";
			$message .= "Prénom : $prenom\n";
			$message .= "Login : $login\n";  
			$message .= "Adresse : $adresse\n";
			$message .= "Email : $email\n";
			$message .= "Téléphone : $tel\n";
			$message .= "Date de naissance : $dateNaissance\n\n";
			$message .= "Pour vous connecter, veuillez cliquer sur ce lien :\n";
			$message .= "https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireConnexion\n\n";
			$message .= "Utilisez votre login ou email pour vous connecter.\n\n";
			$message .= "Cordialement,\nL'équipe du site";
	
			$headers = 'From: ne-pas-repondre@projetshujiang.com' . "\r\n" .
					  'Reply-To: ne-pas-repondre@projetshujiang.com' . "\r\n" .
					  'X-Mailer: PHP/' . phpversion();
	
			if(!mail($to, $subject, $message, $headers)) {
				throw new Exception("Erreur lors de l'envoi de l'email");
			}
	
			// Ajouter le membre
			$idMembre = Membre::ajouterMembre($nom, $prenom, $adresse, $email, $tel, $dateNaissance, $motPasse);
	
			if (!$idMembre) {
				throw new Exception("Échec de l'ajout du membre");
			}
	
			// Ajouter la participation
			$idRole = 7;
			$stmtParticipation = connexion::pdo()->prepare("INSERT INTO Participation (idRole, idMembre, idGroupe) VALUES (?, ?, ?)");
			$success = $stmtParticipation->execute([$idRole, $idMembre, $idGroupe]);
	
			if (!$success) {
				throw new Exception("Échec de l'ajout de la participation");
			}
	
			connexion::pdo()->commit();
			$_SESSION['success'] = "Inscription réussie ! Un email récapitulatif vous a été envoyé.";
			header('Location: routeur.php?controleur=membre&action=afficherFormulaireConnexion');
			exit();
	
		} catch (Exception $e) {
			connexion::pdo()->rollBack();
			error_log("Erreur dans traiterInscriptionInvitation: " . $e->getMessage());
			$_SESSION['erreur'] = "Une erreur est survenue lors de l'inscription. Veuillez réessayer.";
			header('Location: routeur.php?controleur=membre&action=afficherFormulaireInscription&idGroupe=' . $idGroupe);
			exit();
		}
	}




	public static function afficherProfil() {
		$idMembre = isset($_GET['id']) ? $_GET['id'] : null;
	
		if ($idMembre) {
			$membre = Membre::getMembreById($idMembre);
	
			if ($membre) {
				$titre = "Profil de " . $membre->getNomMembre();
				$Style = "StyleProfil";
				include("./vue/debut.php");
				include("./vue/membre/profil.php"); 
				include("./vue/fin.html");
			} else {
				$_SESSION['erreur'] = "Membre introuvable.";
				header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireConnexion');
				exit();
			}
		} else {
			$_SESSION['erreur'] = "Utilisateur non spécifié.";
			header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireConnexion');
			exit();
		}
	}
	
	public static function afficherMembreProfil() {
		$idMembre = isset($_GET['id']) ? $_GET['id'] : null;


		if ($idMembre) {
			$membre = Membre::getMembreById($idMembre);
	
			if ($membre) {
				$titre = "Profil de " . $membre->getNomMembre();
				$Style = "StyleProfil";
				include("./vue/debut.php");
				include("./vue/membre/membreProfil.php"); 
				include("./vue/fin.html");
			} else {
				$_SESSION['erreur'] = "Membre introuvable.";
				header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireConnexion');
				exit();
			}
		} else {
			$_SESSION['erreur'] = "Utilisateur non spécifié.";
			header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireConnexion');
			exit();
		}
	}
	
	
	public static function afficherFormulaireModification() {
		$idMembre = isset($_GET['id']) ? $_GET['id'] : null;
	
		if ($idMembre) {
			$membre = Membre::getMembreById($idMembre);
	
			if ($membre) {
				$titre = "Modifier mes données";
				$Style = "StyleModifierProfil";
				include("./vue/debut.php");
				include("./vue/membre/modifierProfil.php");
				include("./vue/fin.html");
			} else {
				$_SESSION['erreur'] = "Membre introuvable.";
				header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireConnexion');
				exit();
			}
			
        }
    }



	
	public static function traiterModification() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			try {
				$idMembre = $_POST['idMembre'];
				$nom = $_POST['nom'];
				$prenom = $_POST['prenom'];
				$adresse = $_POST['adresse'];
				$email = $_POST['email'];
				$tel = $_POST['tel'];
				$dateNaissance = $_POST['dateNaissance'];
				$nouveauMotDePasse = !empty($_POST['nouveauMotDePasse']) ? $_POST['nouveauMotDePasse'] : null;
	
				if ($nouveauMotDePasse && $nouveauMotDePasse !== $_POST['confirmationMotDePasse']) {
					throw new Exception("Les mots de passe ne correspondent pas.");
				}
	
				Membre::modifierMembre($idMembre, $nom, $prenom, $adresse, $email, $tel, $dateNaissance, $nouveauMotDePasse);
				
				if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== 4) {
					$photoError = Membre::modifierPhoto($idMembre);
					if ($photoError) {
						throw new Exception($photoError);
					}
				}
	
				$_SESSION['success'] = "Profil modifié avec succès.";
				header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherProfil&id=' . $idMembre);
				exit();
			} catch (Exception $e) {
				$_SESSION['erreur'] = $e->getMessage();
				header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireModification&id=' . $idMembre);
				exit();
			}
		}
	}
	
    

        
    public static function deconnecter() {
        session_start();
        session_unset();  
        session_destroy();  
    
        if (isset($_COOKIE['user_id'])) {
            setcookie('user_id', '', time() - 3600, '/');  
        }
    
		header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=membre&action=afficherFormulaireConnexion');
        exit();
    }
	
	public static function supprimerCompte() {
		
		try {
			
			$idMembre = $_GET['id'];
			$idGroupe = $_GET['idGroupe'];
			Membre::supprimerMembreDeGroupe($idMembre, $idGroupe);
			//Participation::supprimerParticipation($idMembre,$idGroupe);
			session_destroy();

			header('Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=discussion&action=lireDiscussion&id=' .$idMembre);
			exit();
		} catch (Exception $e) {

			$_SESSION['erreur'] = "Erreur lors de la suppression : " . $e->getMessage();
			header('Location: ' . $_SERVER['HTTP_REFERER']);
			exit();
		}
	}
    
	public static function retirerMembre() {
		$idM = $_GET['idMembre'];
		$idGroupe = $_GET['idGroupe'];

		// Vérifier si des membres ont été sélectionnés pour suppression
		if (isset($_POST['membres']) && count($_POST['membres']) > 0) {
			$membresASupprimer = $_POST['membres']; 

			try { 
				/*
				$stmtParticipation = connexion::pdo()->prepare("DELETE 
				FROM Participation 
				WHERE IdMembre = ?
				AND IdGroupe = ? ");
				*/

			
				foreach ($membresASupprimer as $idMembre) {
					Membre::supprimerMembreDeGroupe($idMembre, $idGroupe);
					//$stmtParticipation->execute([$idMembre, $idGroupe]);
				
				}

				$_SESSION['message'] = "Le membre ou les membres sélectionnés ont été supprimés.";
				header("Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=parametre&action=lireParametreGroupe&id=$idM&idGroupe=$idGroupe&parametre=listeMembre&classe=membre&ext=php");
				exit();

			} catch (Exception $e) {
				$_SESSION['message'] = "Une erreur est survenue lors de la suppression des membres : " . $e->getMessage();
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit();
			}

		} else {
			$_SESSION['message'] = "Aucun membre n'a été sélectionné pour la suppression.";
			header("Location: https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/routeur.php?controleur=parametre&action=lireParametreGroupe&id=$idM&idGroupe=$idGroupe&parametre=listeMembre&classe=membre&ext=php");
			exit();
		}
	}

}

?>

<?php


use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require_once("./modele/PHPMailer.php");
require_once("./modele/SMTP.php");
require_once("./modele/Exception.php");
require_once("./modele/membre.php");
require_once("./modele/invitation.php");
	
class ControleurInvitation{
	
	public static function envoyerInvitation() {
		
		$email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);
		$idGroupe = $_GET['idGroupe'];
		
			try {
				$token = bin2hex(random_bytes(16));
				$expiration = date('Y-m-d H:i:s', strtotime('+5 day'));
				
				$stmt = connexion::pdo()->prepare("INSERT INTO Invitation (Email, Token, DateExpiration, DateInvitation) VALUES (?, ?, ?, NOW())");
				$stmt->execute([$email, $token, $expiration]);
				
				define('BASE_URL', 'https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG');
				
				$invitationLink = BASE_URL . "/routeur.php?controleur=invitation&action=verifierInvitation&token=$token&idGroupe=$idGroupe";

				$mail = new PHPMailer(true);

				$mail->isSMTP();  
				$mail->Host = 'smtp.gmail.com'; 
				$mail->SMTPAuth = true;
				$mail->Username = 'projetshujiang@gmail.com'; 
				$mail->Password = 'jdez hjhh vpvu ilsh';  
				$mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
				$mail->Port = 587;
				$mail->CharSet = 'UTF-8';
				
				$mail->setFrom('projetshujiang@gmail.com', 'Projet_S301');
				$mail->addAddress($email);

				// Contenu de l'email
				$mail->isHTML(false);  
				$mail->Subject = "Invitation à rejoindre notre groupe";
				$mail->Body    = "Bonjour,\n\nVous avez été invité à rejoindre notre groupe. Cliquez sur le lien ci-dessous pour accepter l'invitation :\n\n$invitationLink\n\nCe lien expirera le $expiration.\n\nCordialement,\nL'équipe.";

				if ($mail->send()) {
					$_SESSION['message'] = "Invitation envoyée avec succès à $email."; 
					header('Location: ' . $_SERVER['HTTP_REFERER']);
					exit();
				} else {
					$_SESSION['message'] = "Échec de l'envoi de l'e-mail."; 
					header('Location: ' . $_SERVER['HTTP_REFERER']);
					exit();
				}
			} catch (Exception $e) {
				$_SESSION['message'] = "Erreur : " . $e->getMessage(); 
			}
			
	}
	
	public static function verifierInvitation() {

		if (isset($_GET['token'])) {
			$token = $_GET['token'];
			try {
               
                $stmt = connexion::pdo()->prepare("SELECT Email, DateExpiration 
				FROM Invitation WHERE Token = ?");
                $stmt->execute([$token]);
                $invitation = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($invitation) {
                    $now = date('Y-m-d H:i:s');

                    if ($now < $invitation['DateExpiration']) {
                        
                        $email = $invitation['Email']; // L'email de l'invité
						$idGroupe = $_GET['idGroupe']; 

						$stmtMembre = connexion::pdo()->prepare("SELECT IdMembre 
						FROM Membre WHERE EmailMembre = ?");
						$stmtMembre->execute([$email]);
						$membreGroupe = $stmtMembre->fetch(PDO::FETCH_ASSOC);
						$idMembre = $membreGroupe['IdMembre'];;
						
						if ($membreGroupe) {
							$idRole = 7; 
			
							$stmt = connexion::pdo()->prepare("INSERT INTO Participation 
							(idRole, idMembre,idGroupe ) VALUES (?, ?, ?)");
							$stmt->execute([$idRole, $idMembre,  $idGroupe]);
							header("Location: routeur.php?controleur=discussion&action=lireDiscussion&id=" . $idMembre . "&idGroupe=" . $idGroupe);
							exit;
						} else {
							header("Location: routeur.php?controleur=membre&action=afficherFormulaireInscription&idGroupe=" . $idGroupe);
							exit;
						}
					} else {
						header("Location: routeur.php?controleur=erreur&action=invitationExpirée");
						exit;
					}
                } else {
                    header("Location: routeur.php"); 
                    exit;
                }
            } catch (Exception $e) {
                echo "Erreur : " . $e->getMessage();
            }
		} else {
			echo "Token manquant.";
		}
	}
}


?>

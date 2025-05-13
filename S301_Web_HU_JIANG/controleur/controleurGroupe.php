<?php
require_once("modele/groupe.php");
require_once("modele/participation.php");

class ControleurGroupe {
    
    public static function afficherFormulaireCreation() {
        if (!isset($_GET['idMembre']) || !is_numeric($_GET['idMembre']) || $_GET['idMembre'] <= 0) {
            header('Location: routeur.php?controleur=membre&action=traiterConnexion');
            exit();
        }
        
        $titre = "Créer un nouveau groupe";
        
        $idMembre = intval($_GET['idMembre']);
        
        $Style = "StyleCreerGroupe";
        include("./vue/debut.php");
        include("./vue/groupe/créerGroupe.php");
        include("./vue/fin.html");
    }
    
	public static function creerGroupe() {
		$idMembre = isset($_POST['idMembre']) ? intval($_POST['idMembre']) : null;
		
		if (!isset($_POST['nomGroupe']) || empty(trim($_POST['nomGroupe']))) {
			throw new Exception("Le nom du groupe est obligatoire");
		}
	
		$imageGroupe = './source/imageGroupe.png'; 
		if (isset($_FILES['imageGroupe']) && $_FILES['imageGroupe']['error'] == 0) {
			$extension = strtolower(pathinfo($_FILES['imageGroupe']['name'], PATHINFO_EXTENSION));
			$allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
			
			if (in_array($extension, $allowedExtensions)) {
				$uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/saes3-shu/S301_Web_HU_JIANG/source/avatarGroupe/';
				if (!is_dir($uploadDirectory)) {
					mkdir($uploadDirectory, 0777, true);
				}
	
				$fileName = time() . '-' . basename($_FILES['imageGroupe']['name']);
				$uploadPath = $uploadDirectory . $fileName;
				
				if (move_uploaded_file($_FILES['imageGroupe']['tmp_name'], $uploadPath)) {
					$imageGroupe = $fileName;
				}
			}
		}
	
		$nomGroupe = trim($_POST['nomGroupe']);
		$couleurGroupe = isset($_POST['couleurGroupe']) ? $_POST['couleurGroupe'] : '#000000';
		$budgetGroupe = isset($_POST['budgetGroupe']) ? floatval($_POST['budgetGroupe']) : 0;
		$themes = isset($_POST['themes']) ? array_filter(array_map('trim', $_POST['themes'])) : [];
		$themeBudgets = isset($_POST['themeBudgets']) ? array_filter(array_map('trim', $_POST['themeBudgets'])) : [];
		
		$dateCreation = date('Y-m-d H:i:s');
		
		$idGroupe = Groupe::creerNouveauGroupe(
			$idMembre,
			$nomGroupe,
			$imageGroupe,
			$couleurGroupe,
			$budgetGroupe,
			implode(',', $themes),
			implode(',', $themeBudgets),
			$dateCreation
		);
		
		if ($idGroupe) {
			header('Location: routeur.php?controleur=discussion&action=lireDiscussion&id=' . $idMembre . '&idGroupe=' . $idGroupe);
			exit();
		} else {
			throw new Exception("Erreur lors de la création du groupe");
		}
	}



	public static function traiterNomGroupe() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$nomGroupe = trim($_POST['nomGroupe']);
			$idMembre = intval($_GET['idMembre']);
			$idGroupe = intval($_GET['idGroupe']);
		
			try {
				
				$query = "SELECT NomGroupe FROM Groupe WHERE IdGroupe = :idGroupe";
				$stmt = connexion::pdo()->prepare($query);
				$stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
				$stmt->execute();
				$result = $stmt->fetch(PDO::FETCH_ASSOC);

				if ($result) {
					$ancienNomGroupe = $result['NomGroupe'];

					if (strcasecmp($ancienNomGroupe, $nomGroupe) == 0) {
						$_SESSION['message'] = "Le nom du groupe est déjà celui-ci.";
						header('Location: ' . $_SERVER['HTTP_REFERER']);
						exit();
					}

					$query = "UPDATE Groupe SET NomGroupe = :nom WHERE IdGroupe = :idGroupe";
					$stmt = connexion::pdo()->prepare($query);
					$stmt->bindParam(':nom', $nomGroupe, PDO::PARAM_STR);
					$stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
					$stmt->execute();

					if ($stmt->rowCount() > 0) {
						$_SESSION['message'] = "Le nom du groupe a été modifié avec succès.";

						header('Location: routeur.php?controleur=parametre&action=lireParametreGroupe&id=' . $idMembre . '&idGroupe=' . $idGroupe.'&parametre=nomGroupe&classe=groupe&ext=html');
						exit();
					} else {
						throw new Exception("Aucune modification n'a été effectuée. Le groupe pourrait déjà avoir ce nom.");
					}
				} else {
					throw new Exception("Le groupe n'existe pas.");
				}
			} catch (Exception $e) {
				session_start();
				$_SESSION['message'] = "Erreur : " . $e->getMessage();
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit();
			}

		}
	}
	
	public static function changerCouleur() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
		
			$idMembre = $_GET['idMembre'];
			$idGroupe = $_GET['idGroupe'];
			$couleurGroupe = $_POST['couleurGroupe'];

			try {
				
				$query = "UPDATE Groupe SET CouleurGroupe = ? 
				WHERE IdGroupe = ?";
				$stmt = connexion::pdo()->prepare($query);
			
				$stmt->execute([$couleurGroupe, $idGroupe]);
				
				
				$_SESSION['message'] = "Couleur modifié avec succès.";
				
				header("Location: routeur.php?controleur=parametre&action=lireParametreGroupe&id=$idMembre&idGroupe=$idGroupe&parametre=couleur&classe=groupe&ext=html");
				exit();
			} catch (Exception $e) {
				session_start();
				$_SESSION['message'] = "Une erreur est survenue lors de la modification de la proposition. Veuillez réessayer.";
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit();
			}
		}
	}
	
	public static function changerImage() {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $idGroupe = $_GET['idGroupe'];
        
        $query = "SELECT ImageGroupe FROM Groupe WHERE IdGroupe = ?";
        $stmt = connexion::pdo()->prepare($query);
        $stmt->execute([$idGroupe]);
        $currentImage = $stmt->fetchColumn();

        if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] == 0) {
            $file = $_FILES['avatar'];

            $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/saes3-shu/S301_Web_HU_JIANG/source/avatarGroupe/';
            
            if (!is_dir($uploadDirectory)) {
                mkdir($uploadDirectory, 0777, true);
            }

            $fileName = time() . '-' . basename($file['name']);
            $uploadPath = $uploadDirectory . $fileName;

            $fileType = mime_content_type($file['tmp_name']);
            if (strpos($fileType, 'image/') !== false) {
                if (move_uploaded_file($file['tmp_name'], $uploadPath)) {
                    $updateQuery = "UPDATE Groupe SET ImageGroupe = ? WHERE IdGroupe = ?";
                    $updateStmt = connexion::pdo()->prepare($updateQuery);
                    $updateStmt->execute([$fileName, $idGroupe]);

                    $_SESSION['message'] = "L'image du groupe a été mise à jour avec succès.";
                } else {
                    $_SESSION['message'] = "Une erreur est survenue lors de la mise à jour de l'image.";
                }
            } else {
                $_SESSION['message'] = "Le fichier sélectionné n'est pas une image valide.";
            }
        } else {
            $_SESSION['message'] = "Aucune modification n'a été apportée. L'image actuelle reste inchangée.";
        }

        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit();
    }
}

}
?>
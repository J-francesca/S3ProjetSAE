<?php
require_once("modele/proposition.php");
require_once("modele/commentaire.php");
require_once("modele/vote.php");
require_once("modele/participation.php");
require_once("modele/groupetheme.php");
require_once("modele/theme.php");
require_once("modele/resultatvote.php");
require_once("modele/resultatvoteformel.php");
class ControleurProposition {

    public static function lireProposition() {
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idProp = intval($_GET['id']);
			
        } else {
            echo "ID de la proposition manquant ou invalide.";
            exit;
        }
    
        if (isset($_GET['idMembre']) && is_numeric($_GET['idMembre'])) {
            $idMembre = intval($_GET['idMembre']);
        } else {
            echo "ID de membre manquant ou invalide.";
            exit;
        }
    
        $idGroupe = Proposition::getIdGroupeFromProposition($idProp);
    
        $proposition = Proposition::getPropositionById($idProp);
		
		$demande = isset($_GET['demande']) && $_GET['demande'] === 'vrai';
        if ($proposition) {
            $stats = Proposition::getStatsForProposition($idProp); 
            $commentaires = Commentaire::getCommentairesByPropositionId($idProp);
            
            
            $voteInfo = ControleurProposition::verifierVote($idProp, $idGroupe, $idMembre);
 
            //Droit supprimer 
            $canDelete = Participation::permisSupprimer($idMembre, $idGroupe);
            $voteExists = $voteInfo['voteExists'];
            $voteStatus = $voteInfo['voteStatus'];
    
            $titre = "Détails de la proposition";
            $Style = "Styleproposition";
            include("./vue/debut.php");
            include("./vue/proposition/uneProposition.php");
            include("./vue/fin.html");

        } else {
            echo "Proposition introuvable.";
        }
		
	}
	
	
    public static function updateLikeDislikeP() {
        if (!isset($_GET['idMembre']) || !is_numeric($_GET['idMembre'])) {
            echo "ID de membre manquant ou invalide.";
            exit;
        }
    
        if (!isset($_GET['idProp'], $_GET['type']) || !is_numeric($_GET['idProp'])) {
            echo "Paramètres manquants ou invalides.";
            exit;
        }
    
        $idMembre = intval($_GET['idMembre']);
        $idProp = intval($_GET['idProp']);
        $type = $_GET['type'];  // 'Like'  'Dislike'
    
        $result = Proposition::updateLikeDislikeForProposition($idProp, $idMembre, $type);
    
        if ($result['success']) {
            header("Location: routeur.php?controleur=proposition&action=lireProposition&id={$idProp}&idMembre={$idMembre}");
        } else {
            header("Location: routeur.php?controleur=proposition&action=lireProposition&id={$idProp}&idMembre={$idMembre}" . urlencode($result['message']));
        }
        exit;
    }
	
    public static function demanderVoteFormel() {
		$idProp = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : exit("ID de la proposition manquant ou invalide.");
		$idMembre = isset($_GET['idMembre']) && is_numeric($_GET['idMembre']) ? intval($_GET['idMembre']) : exit("ID de membre manquant ou invalide.");
		
		try {
			connexion::pdo()->beginTransaction();

			if (!Proposition::incrementNbDemande($idProp)) {
				throw new Exception("Échec de la mise à jour.");
			}

			connexion::pdo()->commit();

			$sql = "INSERT INTO ResultatVoteFormel (IdMembre, IdProp, VoteFormel) 
			VALUES (:idMembre, :idProp, 1)";
			$stmt = connexion::pdo()->prepare($sql);
			$stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
			$stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
			
			$stmt->execute();
			header("Location:routeur.php?controleur=proposition&action=lireProposition&id=$idProp&idMembre=$idMembre");
			exit();
			echo "Vous avez demandé une demande de vote formel. Votre demande a été enregistré.";

		} catch (Exception $e) {
			$pdo->rollBack();
			echo "Erreur lors du traitement de la demande de vote : " . $e->getMessage();
			exit;
		}
	}
	


    public static function verifierVote($idProp, $idGroupe, $idMembre) {
        $idProp = intval($_GET['id']);
        $idMembre = intval($_GET['idMembre']);
        $idGroupe = Proposition::getIdGroupeFromProposition($idProp);


        $voteExists = Vote::checkIfVoteExistsForProposition($idProp);
        $voteStatus = Vote::getStatusByPropositionId($idProp);
    

        $userRole = Participation::getRoleByMembreId($idMembre, $idGroupe);
    

        $canOrganizeVote = in_array($userRole, [1, 2, 6]) && 
        ($voteStatus === 'terminer' || $voteStatus === null || !$voteExists);

        $canRequestVote = (!in_array($userRole, [1, 2, 6])) && 
       ($voteStatus === 'terminer' || $voteStatus === null || !$voteExists);

    
        return [
            'canOrganizeVote' => $canOrganizeVote,
            'canRequestVote' => $canRequestVote,
            'voteExists' => $voteExists,
            'voteStatus' => $voteStatus
        ];
    }



    public static function creerVote() {                    
        $titre = "Créer un vote";
        $Style = "StyleVote";
        include("./vue/debut.php");
        include("./vue/proposition/unVote.php");
        include("./vue/fin.html");
        if (isset($_GET['id']) && is_numeric($_GET['id'])) {
            $idProp = intval($_GET['id']);

            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $dureeLimiteeVote = isset($_POST['dureeLimiteeVote']) ? (int) $_POST['dureeLimiteeVote'] : 24;
                $modeVote = isset($_POST['modeVote']) ? $_POST['modeVote'] : 'majorite';

                $resultat = Vote::createVote($idProp, $dureeLimiteeVote = 24, $modeVote = 'majorite');

                if ($resultat) {

                    header("Location: routeur.php?controleur=proposition&action=lireProposition&id={$idProp}&idMembre={$idMembre}");
                    $message = "Le vote est bien organiser.";
                    exit();
                } else {
                    $message = "Erreur lors de la création du vote. Veuillez réessayer.";
                }
            }


        } else {
            header("Location: routeur.php?controleur=proposition&action=lireProposition&id={$idProp}&idMembre={$idMembre}");
            exit();
        }
    }


    public static function supprimerProposition() {
        if (!isset($_GET['idProp']) || !isset($_GET['idMembre'])) {
            echo "Paramètres manquants.";
            exit;
        }
    
        $idProp = intval($_GET['idProp']);
        $idMembre = intval($_GET['idMembre']);
        $idGroupe = Proposition::getIdGroupeFromProposition($idProp);
    
        if (Participation::permisSupprimer($idMembre, $idGroupe)) {
            $result = Proposition::supprimerProposition($idProp);
            
            if ($result) {
                header("Location: routeur.php?controleur=groupe&action=voirGroupe&id={$idGroupe}");
            } else {
                echo "Erreur lors de la suppression de la proposition.";
            }
        } else {
            echo "Vous n'avez pas la permission de supprimer cette proposition.";
        }
        exit;
    }
    
    public static function supprimerCommentaire() {
        if (!isset($_GET['idComm']) || !isset($_GET['idMembre']) || !isset($_GET['idProp'])) {
            echo "Paramètres manquants.";
            exit;
        }
    
        $idComm = intval($_GET['idComm']);
        $idMembre = intval($_GET['idMembre']);
        $idProp = intval($_GET['idProp']);
        $idGroupe = Proposition::getIdGroupeFromProposition($idProp);
    
        if (Participation::permisSupprimer($idMembre, $idGroupe)) {
            $result = Commentaire::supprimerCommentaire($idComm);
            
            if ($result) {
                header("Location: routeur.php?controleur=proposition&action=lireProposition&id={$idProp}&idMembre={$idMembre}");
            } else {
                echo "Erreur lors de la suppression du commentaire.";
            }
        } else {
            echo "Vous n'avez pas la permission de supprimer ce commentaire.";
        }
        exit;
    }

	
	public static function creerProposition($id,$idGroupe) {
		$titre = "Page de création d'une proposition";
		$tab_gt = Groupe_Theme::getGroupeThemeByIdGroupe($idGroupe);
		$Style = "StyleCreerProp"; 
		
		include("vue/debut.php");
        include("vue/proposition/creerPropositionPartie1.html");
		include("vue/theme/lesThemes.php");
		include("vue/proposition/creerPropositionPartie2.html");
        include("vue/fin.html");
	}

	public static function traiterProposition() {
		if ($_SERVER['REQUEST_METHOD'] == 'POST') {
			$titre = trim($_POST['titre']);
			$description = trim($_POST['description']);
			$idMembre = intval($_GET['idMembre']);
			$idGroupe = intval($_GET['idGroupe']);
			$idTheme = intval($_POST['theme']);
			$frais = intval($_POST['frais']);
			$voteAuto = 0;


			try {
				$idProp = Proposition::ajouterProposition
				($titre, $description, $frais, $idMembre, 
				$idTheme, $voteAuto);

				
				$_SESSION['succes'] = "Proposition ajoutée avec succès.";
				
				header('Location: routeur.php?controleur=discussion&action=lireDiscussionAvecPropositions&id=' . $idMembre . '&idGroupe=' . $idGroupe);
				exit();
			} catch (Exception $e) {
				session_start();
				$_SESSION['erreur'] = "Une erreur est survenue lors de l'ajout de la proposition. Veuillez réessayer.";
				header('Location: ' . $_SERVER['HTTP_REFERER']);
				exit();
			}
		}
	}
	
	public static function demandeVote() {
		$idProp = isset($_GET['id']) && is_numeric($_GET['id']) ? intval($_GET['id']) : exit("ID de la proposition manquant ou invalide.");
		$idMembre = isset($_GET['idMembre']) && is_numeric($_GET['idMembre']) ? intval($_GET['idMembre']) : exit("ID de membre manquant ou invalide.");
		$vote = $_POST['vote'] ?? exit("Aucun vote n'a été sélectionné.");
		$idVote = $_POST['IdVote'] ?? null;
		
		if ($vote === 'oui') {
			
			try {
				$sql = "INSERT INTO ResultatVote (IdMembre, IdVote, Resultat) 
				VALUES (:idMembre, :idVote, 1)";
				$stmt = connexion::pdo()->prepare($sql);
				$stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
				$stmt->bindParam(':idVote', $idVote, PDO::PARAM_INT);
				
				$stmt->execute();
				header("Location:routeur.php?controleur=proposition&action=lireProposition&id=$idProp&idMembre=$idMembre");
				exit();
				echo "Vous avez voté oui pour la proposition ID: $idProp. Votre vote a été enregistré.";

			} catch (PDOException $e) {
				
				echo "Erreur : " . $e->getMessage();
			}
		} elseif ($vote === 'non') {
			try {
				
				$sql = "INSERT INTO ResultatVote (IdMembre, IdVote, Resultat) 
				VALUES (:idMembre, :idVote, 0)";
				
				$stmt = connexion::pdo()->prepare($sql);
				$stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
				$stmt->bindParam(':idVote', $idVote, PDO::PARAM_INT);
				
				$stmt->execute();
				header("Location:routeur.php?controleur=proposition&action=lireProposition&id=$idProp&idMembre=$idMembre");
				exit();
				echo "Vous avez voté non pour la proposition ID: $idProp. Votre vote a été enregistré.";
			} catch (PDOException $e) {
				
				echo "Erreur lors de l'enregistrement du vote : " . $e->getMessage();
			}
		}

	}
}

?>

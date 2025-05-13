<?php
class Membre {
    private $IdMembre;
    private $NomMembre;
    private $PrenomMembre;
    private $AdresseMembre;
    private $EmailMembre;
    private $TelMembre;
    private $DateNaissanceMembre;
    private $DateInscriptionMembre;
    private $LoginMembre;
    private $MotPasseMembre;
    private $Photo;

    public function getIdMembre() { return $this->IdMembre; }
    public function getNomMembre() { return $this->NomMembre; }
    public function getPrenomMembre() { return $this->PrenomMembre; }
    public function getAdresseMembre() { return $this->AdresseMembre; }
    public function getEmailMembre() { return $this->EmailMembre; }
    public function getTelMembre() { return $this->TelMembre; }
    public function getDateNaissanceMembre() { return $this->DateNaissanceMembre; }
    public function getDateInscriptionMembre() { return $this->DateInscriptionMembre; }
    public function getLoginMembre() { return $this->LoginMembre; }
    public function getMotPasseMembre() { return $this->MotPasseMembre; }
    public function getPhoto() { return $this->Photo; }

    public function setIdMembre($idMembre) { $this->IdMembre = $idMembre; }
    public function setNomMembre($nomMembre) { $this->NomMembre = $nomMembre; }
    public function setPrenomMembre($prenomMembre) { $this->PrenomMembre = $prenomMembre; }
    public function setAdresseMembre($adresseMembre) { $this->AdresseMembre = $adresseMembre; }
    public function setEmailMembre($emailMembre) { $this->EmailMembre = $emailMembre; }
    public function setTelMembre($telMembre) { $this->TelMembre = $telMembre; }
    public function setDateNaissanceMembre($dateNaissanceMembre) { $this->DateNaissanceMembre = $dateNaissanceMembre; }
    public function setDateInscriptionMembre($dateInscriptionMembre) { $this->DateInscriptionMembre = $dateInscriptionMembre; }
    public function setLoginMembre($loginMembre) { $this->LoginMembre = $loginMembre; }
    public function setMotPasseMembre($motPasseMembre) { $this->MotPasseMembre = $motPasseMembre; }
    public function setPhoto($photo) { $this->Photo = $photo; }

   
    public function __construct($idMembre = NULL, $nomMembre = NULL, $prenomMembre = NULL, $adresseMembre = NULL, $emailMembre = NULL, $telMembre = NULL, $dateNaissanceMembre = NULL, $dateInscriptionMembre = NULL, $loginMembre = NULL, $motPasseMembre = NULL, $photo = NULL) {
        if (!is_null($idMembre)) {
            $this->IdMembre = $idMembre;
            $this->NomMembre = $nomMembre;
            $this->PrenomMembre = $prenomMembre;
            $this->AdresseMembre = $adresseMembre;
            $this->EmailMembre = $emailMembre;
            $this->TelMembre = $telMembre;
            $this->DateNaissanceMembre = $dateNaissanceMembre;
            $this->DateInscriptionMembre = $dateInscriptionMembre;
            $this->LoginMembre = $loginMembre;
            $this->MotPasseMembre = $motPasseMembre;
            $this->Photo = $photo;
        }
    }

    public static function getAllMembres() {
        $requete = "SELECT * FROM Membre;";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Membre');
        return $resultat->fetchAll();
    }

    public static function getMembreById($id) {
        $requete = "SELECT * FROM Membre WHERE IdMembre = :idMembre;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idMembre', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Membre');
        return $stmt->fetch();
    }
	

    public static function verifierIdentifiants($loginOrEmail, $password)
    {
        $query = "SELECT * FROM Membre WHERE (LoginMembre = :loginOrEmail OR EmailMembre = :loginOrEmail) AND MotPasseMembre = :password";
        $stmt = connexion::pdo()->prepare($query);
        $stmt->bindParam(':loginOrEmail', $loginOrEmail);
        $stmt->bindParam(':password', $password); 
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Membre');
        return $stmt->fetch();
    }
    
    
    public static function verifierEmailExist($email) {
        $query = "SELECT COUNT(*) FROM Membre WHERE EmailMembre = :email";
        $stmt = connexion::pdo()->prepare($query);
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $count = $stmt->fetchColumn();
    
        return $count > 0;
    }
    

	public static function ajouterMembre($nom, $prenom, $adresse, $email, $tel, $dateNaissance, $motPasse) {
		try {
			$login = self::genererLogin($prenom, $nom);
	
			$query = "INSERT INTO Membre (NomMembre, PrenomMembre, AdresseMembre, EmailMembre, TelMembre, DateNaissanceMembre, DateInscriptionMembre, LoginMembre, MotPasseMembre, Photo) 
					  VALUES (:nom, :prenom, :adresse, :email, :tel, :dateNaissance, NOW(), :login, :motPasse, :photo)";
			
			$stmt = connexion::pdo()->prepare($query);
			
			$success = $stmt->execute([
				':nom' => $nom,
				':prenom' => $prenom,
				':adresse' => $adresse,
				':email' => $email,
				':tel' => $tel,
				':dateNaissance' => $dateNaissance,
				':login' => $login,
				':motPasse' => $motPasse,
				':photo' => 'photoprofil.png'  
			]);
	
			if (!$success) {
				throw new Exception("Échec de l'insertion dans la base de données");
			}
	
			$idMembre = connexion::pdo()->lastInsertId();
			
			if (!$idMembre) {
				throw new Exception("Impossible d'obtenir l'ID du nouveau membre");
			}
	
			return $idMembre;
	
		} catch (Exception $e) {
			error_log("Erreur dans ajouterMembre: " . $e->getMessage());
			return false;
		}
	}



    public static function genererLogin($prenom, $nom) {
        $login = strtolower(substr($prenom, 0, 1) . strtolower($nom));
    
        $query = "SELECT COUNT(*) FROM Membre WHERE LoginMembre = :login";
        $stmt = connexion::pdo()->prepare($query);
        $stmt->bindParam(':login', $login);
        $stmt->execute();
        $count = $stmt->fetchColumn();
    
        if ($count > 0) {
            $i = 1;
            while ($count > 0) {
                $login = strtolower(substr($prenom, 0, 1) . strtolower($nom) . $i);
                $stmt->bindParam(':login', $login);
                $stmt->execute();
                $count = $stmt->fetchColumn();
                $i++;
            }
        }
    
        return $login;
    }
    
    
	
	
	

	public static function getIdMembreByLoginOrEmailAndPassword($loginOrEmail, $password)
	{
		
	
		$query = "SELECT * FROM Membre WHERE (LoginMembre = :loginOrEmail OR EmailMembre = :loginOrEmail)";

		$stmt = connexion::pdo()->prepare($query);
		$stmt->bindParam(':loginOrEmail', $loginOrEmail);
		$stmt->execute();

		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Membre');
		$membre = $stmt->fetch();

		return $membre->IdMembre;
	
	}



    public static function modifierPhoto($idMembre) {
        if (!isset($_FILES['photo']) || $_FILES['photo']['error'] == 4) {
            return null;  
        }
    
        if ($_FILES['photo']['error'] !== 0) {
            return "Erreur lors du téléchargement de la photo. Code d'erreur : " . $_FILES['photo']['error'];
        }
    
        $extension = strtolower(pathinfo($_FILES['photo']['name'], PATHINFO_EXTENSION));
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
    
        if (!in_array($extension, $allowedExtensions)) {
            return "Format de fichier non autorisé. Utilisez JPG, JPEG, PNG ou GIF.";
        }
    
        $maxSize = 5 * 1024 * 1024; 
        if ($_FILES['photo']['size'] > $maxSize) {
            return "La taille du fichier dépasse la limite autorisée (5 Mo).";
        }
    
        $uploadDirectory = $_SERVER['DOCUMENT_ROOT'] . '/saes3-shu/S301_Web_HU_JIANG/source/photoprofil/';
        if (!is_dir($uploadDirectory)) {
            mkdir($uploadDirectory, 0777, true);  
        }
    
        $fileName = uniqid() . '-' . basename($_FILES['photo']['name']);
        $uploadPath = $uploadDirectory . $fileName;
    
        if (move_uploaded_file($_FILES['photo']['tmp_name'], $uploadPath)) {
            $query = "UPDATE Membre SET Photo = ? WHERE IdMembre = ?";
            $stmt = connexion::pdo()->prepare($query);
            $stmt->execute([$fileName, $idMembre]);
            return null; 
        }
    
        return "Erreur lors de l'enregistrement de la photo. Veuillez réessayer.";
    }
    


    public static function modifierMembre($idMembre, $nom, $prenom, $adresse, $email, $tel, $dateNaissance, $nouveauMotDePasse = null) {
        try {
            $pdo = connexion::pdo();
            
            $currentData = self::getMembreById($idMembre);
            
            $nom = $nom ?: $currentData->getNomMembre();
            $prenom = $prenom ?: $currentData->getPrenomMembre();
            $adresse = $adresse ?: $currentData->getAdresseMembre();
            $email = $email ?: $currentData->getEmailMembre();
            $tel = $tel ?: $currentData->getTelMembre();
            $dateNaissance = $dateNaissance ?: $currentData->getDateNaissanceMembre();
    
            $query = "UPDATE Membre SET 
                        NomMembre = :nom, 
                        PrenomMembre = :prenom, 
                        AdresseMembre = :adresse, 
                        EmailMembre = :email, 
                        TelMembre = :tel, 
                        DateNaissanceMembre = :dateNaissance";
            
            if ($nouveauMotDePasse) {
                $query .= ", MotPasseMembre = :nouveauMotDePasse";
            }
    
            $query .= " WHERE IdMembre = :idMembre";
            
            $stmt = $pdo->prepare($query);
            $params = [
                ':idMembre' => $idMembre,
                ':nom' => $nom,
                ':prenom' => $prenom,
                ':adresse' => $adresse,
                ':email' => $email,
                ':tel' => $tel,
                ':dateNaissance' => $dateNaissance
            ];
    
            if ($nouveauMotDePasse) {
                $params[':nouveauMotDePasse'] = $nouveauMotDePasse;
            }
    
            $stmt->execute($params);
            return true;
        } catch (Exception $e) {
            throw new Exception("Erreur lors de la mise à jour : " . $e->getMessage());
        }
    }
	
	
	public static function supprimerMembreDeGroupe($idMembre, $idGroupe) {
		$pdo = connexion::pdo();
		
		try {
			// Début de la transaction
			$pdo->beginTransaction();

			// 1. Récupérer les propositions du membre dans ce groupe (via les thèmes du groupe)
			$sqlPropsGroupe = "SELECT p.IdProp 
							  FROM Proposition p
							  INNER JOIN Theme t ON p.IdTheme = t.IdTheme
							  INNER JOIN Groupe_Theme gt ON t.IdTheme = gt.IdTheme
							  WHERE gt.IdGroupe = :idGroupe 
							  AND p.IdMembre = :idMembre";
			$stmtPropsGroupe = $pdo->prepare($sqlPropsGroupe);
			$stmtPropsGroupe->execute(['idGroupe' => $idGroupe, 'idMembre' => $idMembre]);
			$propositionsIds = $stmtPropsGroupe->fetchAll(PDO::FETCH_COLUMN, 0);

			if (!empty($propositionsIds)) {
				$propsStr = implode(',', $propositionsIds);

				// 2. Supprimer les notifications liées aux propositions du membre dans ce groupe
				$sqlNotif = "DELETE FROM Notification 
							WHERE (IdProposition IN ($propsStr))
							AND IdMembre = :idMembre";
				$stmtNotif = $pdo->prepare($sqlNotif);
				$stmtNotif->execute(['idMembre' => $idMembre]);

				// 3. Supprimer les resultatVote liés aux votes sur ces propositions
				$sqlResultatVote = "DELETE rv FROM ResultatVote rv
								  INNER JOIN Vote v ON rv.IdVote = v.IdVote
								  WHERE v.IdProp IN ($propsStr)";
				$stmtResultatVote = $pdo->prepare($sqlResultatVote);
				$stmtResultatVote->execute();

				// 4. Supprimer les votes sur ces propositions
				$sqlVote = "DELETE FROM Vote WHERE IdProp IN ($propsStr)";
				$stmtVote = $pdo->prepare($sqlVote);
				$stmtVote->execute();

				// 5. Supprimer les réactions sur les commentaires de ces propositions
				$sqlReactComm = "DELETE r FROM Reaction r 
							   INNER JOIN Commentaire c ON r.IdComm = c.IdComm 
							   WHERE c.IdProp IN ($propsStr)";
				$stmtReactComm = $pdo->prepare($sqlReactComm);
				$stmtReactComm->execute();

				// 6. Supprimer les réactions directes sur ces propositions
				$sqlReact = "DELETE FROM Reaction WHERE IdProp IN ($propsStr)";
				$stmtReact = $pdo->prepare($sqlReact);
				$stmtReact->execute();

				// 7. Supprimer les signalements sur ces propositions
				$sqlSignal = "DELETE FROM Signalement WHERE IdProp IN ($propsStr)";
				$stmtSignal = $pdo->prepare($sqlSignal);
				$stmtSignal->execute();

				// 8. Supprimer les commentaires sur ces propositions
				$sqlComm = "DELETE FROM Commentaire WHERE IdProp IN ($propsStr)";
				$stmtComm = $pdo->prepare($sqlComm);
				$stmtComm->execute();

				// 9. Supprimer les propositions
				$sqlProp = "DELETE FROM Proposition WHERE IdProp IN ($propsStr)";
				$stmtProp = $pdo->prepare($sqlProp);
				$stmtProp->execute();
			}

			// 10. Supprimer la participation au groupe
			$sqlParticipation = "DELETE FROM Participation 
								WHERE IdMembre = :idMembre 
								AND IdGroupe = :idGroupe";
			$stmtParticipation = $pdo->prepare($sqlParticipation);
			$stmtParticipation->execute(['idMembre' => $idMembre, 'idGroupe' => $idGroupe]);
			
			// Valider la transaction
			$pdo->commit();
			return true;
			
		} catch (Exception $e) {
			// En cas d'erreur, annuler toutes les modifications
			$pdo->rollBack();
			throw new Exception("Erreur lors de la suppression du membre du groupe : " . $e->getMessage());
			return false;
		}
	}

    

}
?>


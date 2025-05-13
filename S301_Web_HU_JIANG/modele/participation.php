<?php
class Participation {
    private $IdRole;
    private $IdMembre;
    private $IdGroupe;

    public function getIdRole() { return $this->IdRole; }
    public function getIdMembre() { return $this->IdMembre; }
    public function getIdGroupe() { return $this->IdGroupe; }

    public function setIdRole($idRole) { $this->IdRole = $IdGroupe; }
    public function setIdMembre($idMembre) { $this->IdMembre = $idMembre; }
    public function setIdGroupe($idGroupe) { $this->IdGroupe = $idGroupe; }

    public function __construct($idRole = NULL, $idMembre = NULL, $idGroupe = NULL) {
        if (!is_null($idRole)) {
            $this->IdRole = $idRole;
            $this->IdMembre = $idMembre;
            $this->IdGroupe = $idGroupe;
        }
    }

    public static function getAllParticipations() {
        $requete = "SELECT * FROM Participation;";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Participation');
        return $resultat->fetchAll();
    }

    public static function getParticipationByIds($idRole, $idMembre, $idGroupe) {
        $requete = "SELECT * FROM Participation WHERE IdRole = :idRole AND IdMembre = :idMembre AND IdGroupe = :idGroupe;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idRole', $idRole, PDO::PARAM_INT);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Participation');
        return $stmt->fetch();
    }




    public static function getRoleByMembreId($idMembre, $idGroupe) {
    
        try {
            $sql = "SELECT IdRole 
                    FROM Participation 
                    WHERE IdMembre = :idMembre AND IdGroupe = :idGroupe";
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
            $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
            $stmt->execute();
    
            $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
            if (!empty($roles)) {
                foreach ($roles as $role) {
                    if (in_array(intval($role), [1, 2,6])) {
                        return true;
                    }
                }
            }
            return false; 
        } catch (Exception $e) {
            error_log("Erreur lors de la vérification des permissions via Participation : " . $e->getMessage());
            return false;
        }
    }
	
	public static function getRoleByIds($idMembre, $idGroupe) {
    
        try {
            $sql = "SELECT IdRole 
                    FROM Participation 
                    WHERE IdMembre = :idMembre 
					AND IdGroupe = :idGroupe";
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
            $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
            $stmt->execute();
    
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

			return $result ? $result['IdRole'] : null;
		} catch (Exception $e) {
			error_log("Erreur lors de la vérification des permissions via Participation : " . $e->getMessage());
			return false;
		}
    }
	
	public static function modifierRole($idMembre, $idGroupe, $idRole) {
    
        try {
            $sql = "UPDATE Participation 
				SET IdRole = :idRole
				WHERE IdMembre = :idMembre
				AND IdGroupe = :idGroupe";
            $stmt = connexion::pdo()->prepare($sql);
			$stmt->bindParam(':idRole', $idRole, PDO::PARAM_INT);
            $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
            $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
            $stmt->execute();

			return true;
		} catch (Exception $e) {
			error_log("Erreur lors de la modification des permissions via Participation : " . $e->getMessage());
			return false;
		}
    }
	
	public static function verifierMembreGroupe($idMembre, $idGroupe) {
		try {
			$sql = "SELECT COUNT(*) as count 
					FROM Participation 
					WHERE IdMembre = :idMembre 
					AND IdGroupe = :idGroupe";
			$stmt = connexion::pdo()->prepare($sql);
			$stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
			$stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
			$stmt->execute();

			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			return $result['count'] > 0; 
		} catch (Exception $e) {
			error_log("Erreur lors de la vérification du membre dans le groupe : " . $e->getMessage());
			return false; 
		}
	}



    public static function permisSupprimer($idMembre, $idGroupe) {
    
        try {
            $sql = "SELECT IdRole 
                    FROM Participation 
                    WHERE IdMembre = :idMembre AND IdGroupe = :idGroupe";
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
            $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
            $stmt->execute();
    
            $roles = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
            if (!empty($roles)) {
                foreach ($roles as $role) {
                    if (in_array(intval($role), [1, 2])) {
                        return true;
                    }
                }
            }
            return false; 
        } catch (Exception $e) {
            error_log("Erreur lors de la vérification des permissions via Participation : " . $e->getMessage());
            return false;
        }
    }
	
	public static function nbMembresDansGroupe($idGroupe)
	{
		
		$query = "SELECT COUNT(*) AS nbMembres 
		FROM Participation
		WHERE IdGroupe = :idG";

		$stmt = connexion::pdo()->prepare($query);
		$stmt->bindParam(':idG', $idGroupe, PDO::PARAM_INT);
		$stmt->execute();

		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$resultat = $stmt->fetch();

		return $resultat['nbMembres'];
	}
    
	
	public static function getMembreByIdGroupe($idGroupe)
	{
		
		$query = "SELECT *
		FROM Participation
		WHERE IdGroupe = :idG";

		$stmt = connexion::pdo()->prepare($query);
		$stmt->bindParam(':idG', $idGroupe, PDO::PARAM_INT);
		$stmt->execute();

		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Participation');
		$resultat = $stmt->fetchAll();

		return $resultat;
	}
	
	
	public static function supprimerParticipation($idMembre,$idGroupe) {
		try {
			$query = "DELETE FROM Participation 
			WHERE IdMembre = :idM
			AND IdGroupe = :idG";

			$stmt = connexion::pdo()->prepare($query);
			$stmt->bindParam(':idM', $idMembre, PDO::PARAM_INT);
			$stmt->bindParam(':idG', $idGroupe, PDO::PARAM_INT);
			$stmt->execute();

			if ($stmt->rowCount() > 0) {
				$_SESSION['success'] = "La participation a été supprimée avec succès.";
			} else {
				$_SESSION['erreur'] = "Aucune participation trouvée avec cet ID.";
			}
		} catch (Exception $e) {
			$_SESSION['erreur'] = "Erreur lors de la suppression de la participation : " . $e->getMessage();
		}
	}
	
	public static function compterMembresParRole($idGroupe, $idRole) {
		try {
			$sql = ($idRole == 2) 
				? "SELECT COUNT(*) as count 
				   FROM Participation 
				   WHERE IdGroupe = :idGroupe 
				   AND (IdRole = 1 OR IdRole = 2)"
				: "SELECT COUNT(*) as count 
				   FROM Participation 
				   WHERE IdGroupe = :idGroupe 
				   AND IdRole = :idRole";

			$stmt = connexion::pdo()->prepare($sql);
			$stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);

			if ($idRole != 2) {
				$stmt->bindParam(':idRole', $idRole, PDO::PARAM_INT);
			}

			$stmt->execute();
			$result = $stmt->fetch(PDO::FETCH_ASSOC);
			return (int) $result['count'];
		} catch (Exception $e) {
			error_log("Erreur lors du comptage des membres par rôle : " . $e->getMessage());
			return 0; 
		}
	}



}
?>

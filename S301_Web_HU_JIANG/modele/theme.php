<?php

class Theme {
    private $IdTheme;
    private $NomTheme;
   
    
    public function getIdTheme() { return $this->IdTheme; }
    public function getNomTheme() { return $this->NomTheme; }
    
    public function setIdTheme($IdTheme) { $this->IdTheme = $idTheme; }
    public function setNomTheme($NomTheme) { $this->NomTheme = $nomTheme; }
    

    public function __construct($idTheme = NULL, $nomTheme = NULL) {
        if (!is_null($idTheme)) {
            $this->IdTheme = $idTheme;
            $this->NomTheme = $nomTheme;
        }
    }

    public static function getAllThemes() {
        $requete = "SELECT * FROM Theme;";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Theme');
        return $resultat->fetchAll();
    }



    public static function getThemeById($id) {
		$requete = "SELECT * FROM Theme WHERE IdTheme = :idTheme";
		$stmt = connexion::pdo()->prepare($requete);
		$stmt->bindParam(':idTheme', $id, PDO::PARAM_INT);
		$stmt->execute();
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Theme');
		
		return $stmt->fetch(); 
	}



    public static function updateBudget($idTheme, $newBudget) {
        $requete = "UPDATE Theme SET BudgetTheme = :budget WHERE IdTheme = :idTheme";
        $stmt = Connexion::pdo()->prepare($requete);
        $stmt->bindParam(':budget', $newBudget, PDO::PARAM_STR);
        $stmt->bindParam(':idTheme', $idTheme, PDO::PARAM_INT);

        if ($stmt->execute()) {
            return "Budget mis à jour avec succès.";
        } else {
            return "Erreur lors de la mise à jour du budget.";
        }
    }


	public static function ajouterTheme($nom) {
		try {
			$requete = "INSERT INTO Theme 
			(NomTheme) VALUES (:nom)";
			$stmt = connexion::pdo()->prepare($requete);
			$stmt->bindParam(':nom', $nom, PDO::PARAM_STR);
			$stmt->execute();
			return true; 
		} catch (Exception $e) {
			error_log("Erreur lors de l'ajout d'un thème : " . $e->getMessage());
			return false; 
		}
	}
	
	public static function getIdThemeByNom($nomTheme) {
    try {
        $requete = "SELECT IdTheme 
                    FROM Theme 
                    WHERE NomTheme = :nomTheme";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':nomTheme', $nomTheme, PDO::PARAM_STR);
        $stmt->execute();
        $idTheme = $stmt->fetchColumn();

        if ($idTheme !== false) {
            return $idTheme; 
        } else {
            return null; 
        }
    } catch (Exception $e) {
        error_log("Erreur lors de la récupération de l'IdTheme : " . $e->getMessage());
        return false;
    }
}



}
?>
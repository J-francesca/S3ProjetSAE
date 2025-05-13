<?php
class Groupe {
    private $IdGroupe;
    private $NomGroupe;
    private $DateCreationGroupe;
    private $ImageGroupe;
    private $CouleurGroupe;
    private $Budget_annuelle;

    public function getIdGroupe() { return $this->IdGroupe; }
    public function getNomGroupe() { return $this->NomGroupe; }
    public function getDateCreationGroupe() { return $this->DateCreationGroupe; }
    public function getImageGroupe() { return $this->ImageGroupe; }
    public function getCouleurGroupe() { return $this->CouleurGroupe; }
    public function getBudgetAnnuel() { return $this->Budget_annuelle; }

    public function setIdGroupe($idGroupe) { $this->IdGroupe = $idGroupe; }
    public function setNomGroupe($nomGroupe) { $this->NomGroupe = $nomGroupe; }
    public function setDateCreationGroupe($dateCreationGroupe) { $this->DateCreationGroupe = $dateCreationGroupe; }
    public function setImageGroupe($imageGroupe) { $this->ImageGroupe = $imageGroupe; }
    public function setCouleurGroupe($couleurGroupe) { $this->CouleurGroupe = $couleurGroupe; }
    public function setBudgetAnnuel($budgetAnnuel) { $this->Budget_annuelle = $budget_annuelle; }

    public function __construct($idGroupe = NULL, $nomGroupe = NULL, $dateCreationGroupe = NULL, $imageGroupe = NULL, $couleurGroupe = NULL, $budget_annuelle = NULL) {
        if (!is_null($idGroupe)) {
            $this->IdGroupe = $idGroupe;
            $this->NomGroupe = $nomGroupe;
            $this->DateCreationGroupe = $dateCreationGroupe;
            $this->ImageGroupe = $imageGroupe;
            $this->CouleurGroupe = $couleurGroupe;
            $this->Budget_annuelle = $budget_annuelle;
        }
    }




    public static function getBudgetAnnuelbyId($idGroupe) {
        try {
            $sql = "SELECT budget FROM groupes WHERE idGroupe =: idGroupe";
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log("Erreur dans la récupération du budget: " . $e->getMessage());
            return 0;
        }
    }


    public static function getAllGroupes() {
        $requete = "SELECT * FROM Groupe;";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Groupe');
        return $resultat->fetchAll();
    }


	public static function getGroupeById($id) {
    $requete = "SELECT * FROM Groupe WHERE IdGroupe = :idGroupe;";
    $stmt = connexion::pdo()->prepare($requete);
    $stmt->bindParam(':idGroupe', $id, PDO::PARAM_INT);
    $stmt->execute();
    $stmt->setFetchMode(PDO::FETCH_CLASS, 'Groupe');
    return $stmt->fetch();
}

    
    public static function getSesGroupes($id) {
		$requete = "CALL groupe_appartient(:idMembre)";
		$stmt = connexion::pdo()->prepare($requete);

		$stmt->bindParam(':idMembre', $id, PDO::PARAM_INT);
		
		$stmt->execute();

		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Groupe');

		return $stmt->fetchAll();
	}
	
	

    public static function creerNouveauGroupe($idMembre, $nomGroupe, $imageGroupe, $couleurGroupe, $budgetGroupe, $themes, $themeBudgets, $dateCreation) {
        try {
            if (empty($idMembre)) {
                throw new Exception("L'ID du membre est invalide.");
            }
            
            $pdo = connexion::pdo();
            $pdo->beginTransaction();
    

            $stmt = $pdo->prepare("INSERT INTO Groupe (NomGroupe, DateCreationGroupe, ImageGroupe, CouleurGroupe, Budget_annuelle) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([
                $nomGroupe,
                $dateCreation,
                $imageGroupe,
                $couleurGroupe,
                $budgetGroupe
            ]);
    
            $idGroupe = $pdo->lastInsertId();
    
            $stmt = $pdo->prepare("INSERT INTO Participation (IdRole, IdMembre, IdGroupe) VALUES (1, ?, ?)");
            $stmt->execute([$idMembre, $idGroupe]);
    
            if (!empty($themes) && !empty($themeBudgets)) {
                $themesArray = explode(',', $themes);
                $themeBudgetsArray = explode(',', $themeBudgets);
                
                for ($i = 0; $i < count($themesArray); $i++) {
                    $theme = trim($themesArray[$i]);
                    $themeBudget = floatval(trim($themeBudgetsArray[$i]));
                    
                    if (!empty($theme)) {
                        $stmt = $pdo->prepare("INSERT INTO Theme (NomTheme) VALUES (?)");
                        $stmt->execute([$theme]);
                        
                        $idTheme = $pdo->lastInsertId();
                        
                        $stmt = $pdo->prepare("INSERT INTO Groupe_Theme (IdGroupe, IdTheme, BudgetTheme) VALUES (?, ?, ?)");
                        $stmt->execute([$idGroupe, $idTheme, $themeBudget]);
                    }
                }
            }
    
            $pdo->commit();
            return $idGroupe;
    
        } catch (PDOException $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }
    
    
 
}
?>

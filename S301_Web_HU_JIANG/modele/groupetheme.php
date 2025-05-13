<?php
class Groupe_Theme {
    private $IdGroupe;
    private $IdTheme;
	private $BudgetTheme;

    public function getIdGroupe() { return $this->IdGroupe; }
    public function getIdTheme() { return $this->IdTheme; }
	public function getBudgetTheme() { return $this->BudgetTheme; }

    public function setIdGroupe($idGroupe) { $this->IdGroupe = $idGroupe; }
    public function setIdTheme($idTheme) { $this->IdTheme = $idTheme; }
	public function setBudgetTheme($BudgetTheme) { $this->BudgetTheme = $budgetTheme; }

    
    public function __construct($idGroupe = NULL, $idTheme = NULL, $budgetTheme = NULL) {
        if (!is_null($idGroupe)) {
            $this->IdGroupe = $idGroupe;
            $this->IdTheme = $idTheme;
			$this->BudgetTheme = $budgetTheme;
        }
    }

    public static function getAllGroupeThemes() {
        $requete = "SELECT * FROM Groupe_Theme;";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Groupe_Theme');
        return $resultat->fetchAll();
    }

    public static function getGroupeThemeByIds($idGroupe, $idTheme) {
        $requete = "SELECT * 
		FROM Groupe_Theme
		WHERE IdGroupe = :idGroupe 
		AND IdTheme = :idTheme";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->bindParam(':idTheme', $idTheme, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Groupe_Theme');
        return $stmt->fetch();
    }
	
	
	public static function getGroupeThemeByIdGroupe($idGroupe) {
        $requete = "SELECT * 
		FROM Groupe_Theme 
		WHERE IdGroupe = :idGroupe";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Groupe_Theme');
        return $stmt->fetchAll(); 
    }
	
	public static function ajouterGroupeTheme($idGroupe, $idTheme, $budget) {
		try {
			$requete = "INSERT INTO Groupe_Theme 
			(IdGroupe, IdTheme, BudgetTheme) 
			VALUES (:idGroupe, :idTheme, :budget)";
			$stmt = connexion::pdo()->prepare($requete);
			$stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
			$stmt->bindParam(':idTheme', $idTheme, PDO::PARAM_INT);
			$stmt->bindParam(':budget', $budget, PDO::PARAM_INT);
			$stmt->execute();
			return true;
		} catch (Exception $e) {
			error_log("Erreur lors de l'ajout d'un groupe-thème : " . $e->getMessage());
			return false; 
		}
	}
	
	
	public static function supprimerPropositionTheme($idProp) {
    $pdo = connexion::pdo();
    
    try {
        // Début de la transaction
        $pdo->beginTransaction();
        
        // 1. Supprimer les résultats de vote liés aux votes de la proposition
        $sqlResultatVote = "DELETE rv FROM ResultatVote rv 
                           INNER JOIN Vote v ON rv.IdVote = v.IdVote 
                           WHERE v.IdProp = :idProp";
        $stmtResultatVote = $pdo->prepare($sqlResultatVote);
        $stmtResultatVote->execute(['idProp' => $idProp]);
        
        // 2. Supprimer les votes liés à la proposition
        $sqlVote = "DELETE FROM Vote WHERE IdProp = :idProp";
        $stmtVote = $pdo->prepare($sqlVote);
        $stmtVote->execute(['idProp' => $idProp]);
        
        // 3. Supprimer les réactions liées aux commentaires de la proposition
        $sqlReactComm = "DELETE r FROM Reaction r 
                        INNER JOIN Commentaire c ON r.IdComm = c.IdComm 
                        WHERE c.IdProp = :idProp";
        $stmtReactComm = $pdo->prepare($sqlReactComm);
        $stmtReactComm->execute(['idProp' => $idProp]);
        
        // 4. Supprimer les réactions directement liées à la proposition
        $sqlReactProp = "DELETE FROM Reaction WHERE IdProp = :idProp";
        $stmtReactProp = $pdo->prepare($sqlReactProp);
        $stmtReactProp->execute(['idProp' => $idProp]);
        
        // 5. Supprimer les commentaires liés à la proposition
        $sqlComm = "DELETE FROM Commentaire WHERE IdProp = :idProp";
        $stmtComm = $pdo->prepare($sqlComm);
        $stmtComm->execute(['idProp' => $idProp]);
        
        // 6. Supprimer la proposition elle-même
        $sqlProp = "DELETE FROM Proposition WHERE IdProp = :idProp";
        $stmtProp = $pdo->prepare($sqlProp);
        $stmtProp->execute(['idProp' => $idProp]);
        
        // Valider la transaction
        $pdo->commit();
        return true;
        
    } catch (Exception $e) {
        // En cas d'erreur, annuler toutes les modifications
        $pdo->rollBack();
        throw new Exception("Erreur lors de la suppression de la proposition : " . $e->getMessage());
        return false;
    }
}
	
	
}
?>

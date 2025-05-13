<?php
class ResultatVote {
    private $IdMembre;
    private $IdVote;
    private $Resultat;

    public function getIdMembre() { return $this->IdMembre; }
    public function getIdVote() { return $this->IdVote; }
    public function getResultat() { return $this->Resultat; }

    public function setResultat($resultat) { $this->resultat = $resultat; }

    public function __construct($idMembre = NULL, $idVote = NULL, $resultat = NULL) {
        if (!is_null($idMembre)) {
            $this->IdMembre = $idMembre;
            $this->IdVote = $idVote;
            $this->Resultat = $resultat;
        }
    }

    
    public static function getAllResultats($idGroupe) {
        try {
            $sql = "SELECT DISTINCT rv.IdMembre, rv.IdProp, rv.Resultat 
                    FROM ResultatVote rv
                    INNER JOIN Vote v ON rv.IdVote = v.IdVote
                    INNER JOIN Proposition p ON v.IdProp = p.IdProp
                    INNER JOIN Theme t ON p.IdTheme = t.IdTheme
                    INNER JOIN Groupe_Theme gt ON t.IdTheme = gt.IdTheme
                    WHERE gt.IdGroupe = :idGroupe";
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans la récupération des résultats de vote: " . $e->getMessage());
            return [];
        }
    }
    

    public static function getResultatById($idMembre, $idVote) {
        $requete = "SELECT * FROM ResultatVote WHERE IdMembre = :idMembre AND IdVote = :idVote;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idVote', $idVote, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'ResultatVote');
        return $stmt->fetch();
    }
	
	public static function getResultatByIdVote($idVote) {
        $requete = "SELECT * 
		FROM ResultatVote 
		WHERE IdVote = :idVote;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idVote', $idVote, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'ResultatVote');
        return $stmt->fetchAll(); 
    }
}
?>

<?php

require_once 'proposition.php';
require_once 'participation.php';

class ResultatVoteFormel {
    private $IdMembre;
	private $IdProp;
    private $VoteFormel;

    public function getIdMembre() { return $this->IdMembre; }
    public function getIdProp() { return $this->IdProp; }
    public function getVoteFormel() { return $this->VoteFormel; }

    public function setResultat($voteFormel) { $this->VoteFormel = $voteFormel; }

    public function __construct($idMembre = NULL, $idProp = NULL, $voteFormel = NULL) {
        if (!is_null($idMembre)) {
            $this->IdMembre = $idMembre;
            $this->IdProp = $idProp;
            $this->VoteFormel = $voteFormel;
        }
    }

    /*
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
    */
	/*
    public static function getResultatById($idMembre, $idVote) {
        $requete = "SELECT * FROM ResultatVote WHERE IdMembre = :idMembre AND IdVote = :idVote;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idVote', $idVote, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'ResultatVote');
        return $stmt->fetch();
    }
	*/
	/*
	public static function getResultatByIdVote($idProp, $idMembre, $idGroupe) {
        $requete = "SELECT * 
		FROM ResultatVoteFormel 
		WHERE IdVote = :idVote;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idVote', $idVote, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'ResultatVoteFormel');
        return $stmt->fetchAll(); 
    }
	*/
	
	public static function existeDemandeVoteFormel($idProp, $idMembre) {
        
        $sql = "SELECT COUNT(*) 
                FROM ResultatVoteFormel
                WHERE IdMembre = :idMembre
				AND IdProp = :idProp ";
                  
      
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute([
            'idMembre' => $idMembre,
			'idProp' => $idProp
			
        ]);
       
        return $stmt->fetchColumn() > 0;
    }
    
	
    public static function verifierNbVoteFormel($idProp,$idGroupe) {
		$proposition  = Proposition::getPropositionByIdProp($idProp);

		$nbVote = $proposition ->getNbDemande();
		
		$nbVoteTotal = Participation::nbMembresDansGroupe($idGroupe);
		$seuil = 0.7 * $nbVoteTotal; // Calcul des 70 % du total de demandes

		// Retourne true si le nombre de votes atteint ou dépasse 70 %, sinon false
		return $nbVote >= $seuil;
	}
}
?>

<?php
class Vote {
    private $IdVote;
    private $DateVote;
    private $DureeLimiteeVote;
    private $ModeVote;
    private $IdProp;
    private $Status;

    public function getIdVote() { return $this->IdVote; }
    public function getDateVote() { return $this->DateVote; }
    public function getDureeLimiteeVote() { return $this->DureeLimiteeVote; }
    public function getModeVote() { return $this->ModeVote; }
    public function getIdProp() { return $this->IdProp; }
    public function getStatus() { return $this->Status; }

    public function setDateVote($dateVote) { $this->DateVote = $dateVote; }
    public function setDureeLimiteeVote($duree) { $this->DureeLimiteeVote = $duree; }
    public function setModeVote($mode) { $this->ModeVote = $mode; }
    public function setIdProp($idProp) { $this->IdProp = $idProp; }
    public function setStatus($status) { $this->Status = $status; }

    public function __construct($idVote = NULL, $dateVote = NULL, $duree = NULL, $mode = NULL, $idProp = NULL, $status = NULL) {
        if (!is_null($idVote)) {
            $this->IdVote = $idVote;
            $this->DateVote = $dateVote;
            $this->DureeLimiteeVote = $duree;
            $this->ModeVote = $mode;
            $this->IdProp = $idProp;
            $this->Status = $status;
        }
    }

    public static function getAllVotes() {
        $requete = "SELECT * FROM Vote;";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Vote');
        return $resultat->fetchAll();
    }

    public static function getVoteById($idVote) {
        $requete = "SELECT * FROM Vote WHERE IdVote = :idVote;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idVote', $idVote, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Vote');
        return $stmt->fetch();
    }
	
	public static function getVoteByIdProp($idProp) {
        $requete = "SELECT * 
		FROM Vote 
		WHERE IdProp = :idProp;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Vote');
        return $stmt->fetch();
    }


    public static function checkIfVoteExistsForProposition($idProp) {
        $requete = "SELECT COUNT(*) FROM Vote WHERE IdProp = :idProp AND status = 'en cours'";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
        $stmt->execute();
    
        $result = $stmt->fetchColumn(); 
    
        return $result > 0; 
    }

    
    public static function getStatusByPropositionId($idProp) {
        $requete = "SELECT Status FROM Vote WHERE IdProp = :idProp ORDER BY DateVote DESC LIMIT 1";
        $stmt = Connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchColumn();  
    }
    


    public static function updateStatus($idVote, $status) {
        $requete = "UPDATE Vote SET Status = :status WHERE IdVote = :idVote;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':status', $status, PDO::PARAM_STR);
        $stmt->bindParam(':idVote', $idVote, PDO::PARAM_INT);
        return $stmt->execute();
    }


    public static function createVote($idProp, $dureeLimiteeVote = 24, $modeVote = 'majorite') {
        $dateVote = date('Y-m-d H:i:s');
        
        $requete = "INSERT INTO Vote (idProp, DateVote, DureeLimiteeVote, ModeVote, Status) 
                    VALUES (:idProp, :DateVote, :DureeLimiteeVote, :ModeVote, 'en cours')";
    
        $stmt = Connexion::pdo()->prepare($requete);
    
        $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
        $stmt->bindParam(':DateVote', $dateVote, PDO::PARAM_STR); 
        $stmt->bindParam(':DureeLimiteeVote', $dureeLimiteeVote, PDO::PARAM_INT);  
        $stmt->bindParam(':ModeVote', $modeVote, PDO::PARAM_STR);  
    
        if ($stmt->execute()) {
            return ['idVote' => Connexion::pdo()->lastInsertId()];
        }
    
        return false;
    }
	
	public static function existeDemandeVote($idVote, $idMembre) {
        
        $sql = "SELECT COUNT(*) 
                FROM ResultatVote
                WHERE IdVote = :idVote 
                AND IdMembre = :idMembre";
                  
      
        $stmt = Connexion::pdo()->prepare($sql);
        $stmt->execute([
            'idVote' => $idVote,
            'idMembre' => $idMembre
        ]);
       
        return $stmt->fetchColumn() > 0;
    }
	
	
}
?>

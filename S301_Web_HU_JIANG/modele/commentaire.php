<?php
class Commentaire {
    private $IdComm;
    private $DateComm;
    private $ContenuComm;
    private $IdMembre;
    private $IdProp;
    private $IdCommParent;

    public function getIdComm() { return $this->IdComm; }
    public function getDateComm() { return $this->DateComm; }
    public function getContenuComm() { return $this->ContenuComm; }
    public function getIdMembre() { return $this->IdMembre; }
    public function getIdProp() { return $this->IdProp; }
    public function getIdCommParent() { return $this->IdCommParent; }

    public function setDateComm($date) { $this->DateComm = $date; }
    public function setContenuComm($contenu) { $this->ContenuComm = $contenu; }
    public function setIdMembre($idMembre) { $this->IdMembre = $idMembre; }
    public function setIdProp($idProp) { $this->IdProp = $idProp; }
    public function setIdCommParent($idCommParent) { $this->IdCommParent = $idCommParent; }

    public function __construct($idComm, $dateComm, $contenuComm, $idMembre, $idProp, $idCommParent) {
        $this->IdComm = $idComm;
        $this->DateComm = $dateComm;
        $this->DontenuComm = $contenuComm;
        $this->IdMembre = $idMembre;
        $this->IdProp = $idProp;
        $this->IdCommParent = $idCommParent;
    }

    public static function getAllCommentaires() {
        $requete = "SELECT * FROM Commentaire;";
        $resultat = Connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Commentaire');
        return $resultat->fetchAll();
    }

    public static function getCommentaireById($idComm) {
        $requete = "SELECT * FROM Commentaire WHERE IdComm = :idComm;";
        $stmt = Connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idComm', $idComm, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public static function getCommentairesByPropositionId($idProp) {
        $query = "SELECT c.IdComm, c.DateComm, c.ContenuComm, c.IdMembre, m.NomMembre, m.PrenomMembre, c.IdProp, c.IdCommParent
                  FROM Commentaire c
                  JOIN Membre m ON c.IdMembre = m.IdMembre
                  WHERE c.IdProp = :idProp";
        $stmt = Connexion::pdo()->prepare($query);
        $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
        $stmt->execute();

        $commentaires = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $stats = self::getStatsForCommentaire($row['IdComm']);
            $commentaires[] = [
                'IdComm' => $row['IdComm'],
                'DateComm' => $row['DateComm'],
                'ContenuComm' => $row['ContenuComm'],
                'IdMembre' => $row['IdMembre'],
                'IdProp' => $row['IdProp'],
                'IdCommParent' => $row['IdCommParent'],
                'NomMembre' => $row['NomMembre'],
                'PrenomMembre' => $row['PrenomMembre'],
                'LikeCount' => $stats['likeCount'],
                'DislikeCount' => $stats['dislikeCount']
            ];
        }
        return $commentaires;
    }

    public static function getStatsForCommentaire($idComm) {
        $query = "CALL GetStatsForCommentaire(:idComm)";
        $stmt = Connexion::pdo()->prepare($query);
        $stmt->bindParam(':idComm', $idComm, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'likeCount' => $result['LikeCount'] ?? 0,
            'dislikeCount' => $result['DislikeCount'] ?? 0
        ];
    }




    public static function updateLikeDislikeForCommentaire($idComm, $idMembre,$type) {
        $checkVoteQuery = "SELECT * FROM Reaction 
        WHERE IdMembre = :idMembre AND IdComm = :idComm";
        $stmt = Connexion::pdo()->prepare($checkVoteQuery);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idComm', $idComm, PDO::PARAM_INT);
        $stmt->execute();
        $existingReaction = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existingReaction) {
        if ($existingReaction['NomReact'] === $type) {
        $deleteQuery = "DELETE FROM Reaction 
                    WHERE IdMembre = :idMembre AND IdComm = :idComm";
        $stmt = Connexion::pdo()->prepare($deleteQuery);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idComm', $idComm, PDO::PARAM_INT);
        $stmt->execute();

        return ['success' => true, 'action' => 'removed'];
        } 
        else {
        $updateQuery = "UPDATE Reaction 
                    SET NomReact = :type, DateReact = NOW() 
                    WHERE IdMembre = :idMembre AND IdComm = :idComm";
        $stmt = Connexion::pdo()->prepare($updateQuery);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idComm', $idComm, PDO::PARAM_INT);
        $stmt->execute();

        return ['success' => true, 'action' => 'updated'];
        }
        }

        $insertReactionQuery = "INSERT INTO Reaction (NomReact, DateReact, IdMembre, IdComm) 
                    VALUES (:type, NOW(), :idMembre, :idComm)";
        $stmt = Connexion::pdo()->prepare($insertReactionQuery);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idComm', $idComm, PDO::PARAM_INT);
        $stmt->execute();

        return ['success' => true, 'action' => 'added'];
        }


      public static function getUserReactionForCommentaire($idComm, $idMembre) {
        $query = "SELECT NomReact FROM Reaction 
                  WHERE IdMembre = :idMembre AND IdComm = :idComm";
        $stmt = Connexion::pdo()->prepare($query);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idComm', $idComm, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['NomReact'] : null;
    }




      public static function ajouterCommentaire($idMembre, $idProp, $contenuComm) {
        $dateComm = date('Y-m-d H:i:s'); 
        $query = "INSERT INTO Commentaire (DateComm, ContenuComm, IdMembre, IdProp) 
                  VALUES (:dateComm, :contenuComm, :idMembre, :idProp)";
        $stmt = Connexion::pdo()->prepare($query);
        $stmt->bindParam(':dateComm', $dateComm);
        $stmt->bindParam(':contenuComm', $contenuComm);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
        $stmt->execute();
    
        return ['success' => true];
    }
    
    
    public static function supprimerCommentaire($idComm) {
        try {
            $pdo = connexion::pdo();
            $pdo->beginTransaction();
    
            // Suppression des réactions liées au commentaire
            $sqlReactions = "DELETE FROM Reaction WHERE IdComm = :idComm";
            $stmtReactions = $pdo->prepare($sqlReactions);
            $stmtReactions->bindParam(':idComm', $idComm, PDO::PARAM_INT);
            $stmtReactions->execute();
    
            // Suppression du commentaire
            $sqlCommentaire = "DELETE FROM Commentaire WHERE IdComm = :idComm";
            $stmtCommentaire = $pdo->prepare($sqlCommentaire);
            $stmtCommentaire->bindParam(':idComm', $idComm, PDO::PARAM_INT);
            $stmtCommentaire->execute();
    
            // Si tout s'est bien passé, on valide la transaction
            $pdo->commit();
            return true;
    
        } catch (Exception $e) {
            // En cas d'erreur, on annule la transaction
            $pdo->rollBack();
            error_log("Erreur lors de la suppression du commentaire et ses réactions : " . $e->getMessage());
            return false;
        }
    }



    }




    

?>

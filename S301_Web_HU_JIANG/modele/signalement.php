<?php
class Signalement {
    private $IdSignal;
    private $DateSignal;
    private $RaisonSignal;
    private $IdMembre;
    private $IdProp;
    private $IdComm;

    public function getIdSignal() { return $this->IdSignal; }
    public function getDateSignal() { return $this->DateSignal; }
    public function getRaisonSignal() { return $this->RaisonSignal; }
    public function getIdMembre() { return $this->IdMembre; }
    public function getIdProp() { return $this->IdProp; }
    public function getIdComm() { return $this->IdComm; }

    public function setDateSignal($dateSignal) { $this->DateSignal = $dateSignal; }
    public function setRaisonSignal($raisonSignal) { $this->RaisonSignal = $raisonSignal; }
    public function setIdMembre($idMembre) { $this->IdMembre = $idMembre; }
    public function setIdProp($idProp) { $this->IdProp = $idProp; }
    public function setIdComm($idComm) { $this->IdComm = $idComm; }

    public function __construct($idSignal = NULL, $dateSignal = NULL, $raisonSignal = NULL, $idMembre = NULL, $idProp = NULL, $idComm = NULL) {
        if (!is_null($idSignal)) {
            $this->IdSignal = $idSignal;
            $this->DateSignal = $dateSignal;
            $this->RaisonSignal = $raisonSignal;
            $this->IdMembre = $idMembre;
            $this->IdProp = $idProp;
            $this->IdComm = $idComm;
        }
    }

    public static function getAllSignalements() {
        $requete = "SELECT * FROM Signalement;";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Signalement');
        return $resultat->fetchAll();
    }

    public static function getSignalementById($idSignal) {
        $requete = "SELECT * 
                    FROM Signalement 
                    WHERE IdSignal = :idSignal;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idSignal', $idSignal, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Signalement');
        return $stmt->fetch();
    }



    public static function getSignalementByIdGroupe($idGroupe) {
        $requete = "SELECT DISTINCT s.IdSignal, s.DateSignal, s.RaisonSignal, s.IdMembre, s.IdProp, s.IdComm 
        FROM Signalement s 
        LEFT JOIN Proposition p ON (p.IdProp = s.IdProp OR p.IdProp = (SELECT IdProp FROM Commentaire WHERE IdComm = s.IdComm))
        LEFT JOIN Groupe_Theme gt ON p.IdTheme = gt.IdTheme
        LEFT JOIN Groupe g ON g.IdGroupe = gt.IdGroupe
        WHERE g.IdGroupe = :idGroupe";
        
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
        
        $stmt->execute();
    
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Signalement');
        return $stmt->fetchAll();
    }



    public static function getSignalementByType($type) {
        $sql = "SELECT s.*, p.TitreProp, c.ContenuComm 
                FROM Signalement s
                LEFT JOIN Proposition p ON s.IdProp = p.IdProp
                LEFT JOIN Commentaire c ON s.IdComm = c.IdComm
                WHERE " . ($type === 'commentaire' ? 's.IdComm IS NOT NULL' : 's.IdProp IS NOT NULL');
        
        $stmt = connexion::pdo()->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }







    public static function ajouterSignalementComm($signalement) {
        $dateSignal = $_POST['dateSignal'];
        
        $query = "INSERT INTO Signalement (DateSignal, RaisonSignal, IdMembre, IdComm)
                VALUES (:dateSignal, :raisonSignal, :idMembre, :idComm)";
        
        try {
            $stmt = Connexion::pdo()->prepare($query);
            $stmt->bindParam(':dateSignal', $dateSignal);
            $stmt->bindParam(':raisonSignal', $_POST['raisonSignal']);
            $stmt->bindParam(':idMembre', $_POST['idMembre']);
            $stmt->bindParam(':idComm', $_POST['idComm']);
            $stmt->execute();
            
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout du signalement commentaire: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public static function ajouterSignalementProp($signalement) {
        $dateSignal = $_POST['dateSignal'];
        
        $query = "INSERT INTO Signalement (DateSignal, RaisonSignal, IdMembre, IdProp)
                VALUES (:dateSignal, :raisonSignal, :idMembre, :idProp)";
        
        try {
            $stmt = Connexion::pdo()->prepare($query);
            $stmt->bindParam(':dateSignal', $dateSignal);
            $stmt->bindParam(':raisonSignal', $_POST['raisonSignal']);
            $stmt->bindParam(':idMembre', $_POST['idMembre']);
            $stmt->bindParam(':idProp', $_POST['idProp']);
            $stmt->execute();
            
            return ['success' => true];
        } catch (PDOException $e) {
            error_log("Erreur lors de l'ajout du signalement proposition: " . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }


    public static function supprimerSignalementParIdComm($idComm) {
        try {
            $requete = "DELETE FROM Signalement WHERE IdComm = :idComm";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->bindParam(':idComm', $idComm, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erreur suppression signalement par IdComm: " . $e->getMessage());
            return false;
        }
    }
    
    public static function supprimerSignalementParIdProp($idProp) {
        try {
            $requete = "DELETE FROM Signalement WHERE IdProp = :idProp";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erreur suppression signalement par IdProp: " . $e->getMessage());
            return false;
        }
    }
    
    public static function supprimerSignalement($idSignal) {
        try {
            $requete = "DELETE FROM Signalement WHERE IdSignal = :idSignal";
            $stmt = connexion::pdo()->prepare($requete);
            $stmt->bindParam(':idSignal', $idSignal, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (Exception $e) {
            error_log("Erreur suppression signalement: " . $e->getMessage());
            return false;
        }
    }






}
?>

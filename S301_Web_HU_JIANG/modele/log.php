<?php
class Log {
    private $idLog;
    private $idAuteur;
    private $action;
    private $dateHeureAction;
    private $ligneAvant;
    private $ligneApres;

    public function getIdLog() { return $this->idLog; }
    public function getIdAuteur() { return $this->idAuteur; }
    public function getAction() { return $this->action; }
    public function getDateHeureAction() { return $this->dateHeureAction; }
    public function getLigneAvant() { return $this->ligneAvant; }
    public function getLigneApres() { return $this->ligneApres; }

    
    public function setIdAuteur($idAuteur) { $this->idAuteur = $idAuteur; }
    public function setAction($action) { $this->action = $action; }
    public function setLigneAvant($ligneAvant) { $this->ligneAvant = $ligneAvant; }
    public function setLigneApres($ligneApres) { $this->ligneApres = $ligneApres; }

    public function __construct($idLog = NULL, $idAuteur = NULL, $action = NULL, $dateHeureAction = NULL, $ligneAvant = NULL, $ligneApres = NULL) {
        if (!is_null($idLog)) {
            $this->idLog = $idLog;
            $this->idAuteur = $idAuteur;
            $this->action = $action;
            $this->dateHeureAction = $dateHeureAction;
            $this->ligneAvant = $ligneAvant;
            $this->ligneApres = $ligneApres;
        }
    }

    public static function getAllLogs() {
        $requete = "SELECT * FROM LOG;";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Log');
        return $resultat->fetchAll();
    }

    public static function getLogById($idLog) {
        $requete = "SELECT * FROM LOG WHERE idLog = :idLog;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idLog', $idLog, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Log');
        return $stmt->fetch();
    }
}
?>

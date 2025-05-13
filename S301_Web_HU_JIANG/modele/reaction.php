<?php
class Reaction {
    private $idReact;
    private $nomReact;
    private $dateReact;
    private $idMembre;
    private $idProp;
    private $idComm;

    public function getIdReact() { return $this->idReact; }
    public function getNomReact() { return $this->nomReact; }
    public function getDateReact() { return $this->dateReact; }
    public function getIdMembre() { return $this->idMembre; }
    public function getIdProp() { return $this->idProp; }
    public function getIdComm() { return $this->idComm; }

    
    public function setNomReact($nomReact) { $this->nomReact = $nomReact; }
    public function setDateReact($dateReact) { $this->dateReact = $dateReact; }
    public function setIdMembre($idMembre) { $this->idMembre = $idMembre; }
    public function setIdProp($idProp) { $this->idProp = $idProp; }
    public function setIdComm($idComm) { $this->idComm = $idComm; }

    public function __construct($idReact = NULL, $nomReact = NULL, $dateReact = NULL, $idMembre = NULL, $idProp = NULL, $idComm = NULL) {
        if (!is_null($idReact)) {
            $this->idReact = $idReact;
            $this->nomReact = $nomReact;
            $this->dateReact = $dateReact;
            $this->idMembre = $idMembre;
            $this->idProp = $idProp;
            $this->idComm = $idComm;
        }
    }

    public static function getAllReactions() {
        $requete = "SELECT * FROM Reaction;";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Reaction');
        return $resultat->fetchAll();
    }

    public static function getReactionById($idReact) {
        $requete = "SELECT * FROM Reaction WHERE IdReact = :idReact;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idReact', $idReact, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Reaction');
        return $stmt->fetch();
    }
}
?>

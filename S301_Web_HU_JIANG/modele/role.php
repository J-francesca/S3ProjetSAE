<?php

class Role {
    private $idRole;
    private $nomRole;

    public function getIdRole() { return $this->idRole; }
    public function getNomRole() { return $this->nomRole; }

    public function setIdRole($idRole) { $this->idRole = $idRole; }
    public function setNomRole($nomRole) { $this->nomRole = $nomRole; }

    public function __construct($idRole = NULL, $nomRole = NULL) {
        if (!is_null($idRole)) {
            $this->idRole = $idRole;
            $this->nomRole = $nomRole;
        }
    }

    public static function getAllRoles() {
        $requete = "SELECT * FROM Role;";
        $resultat = connexion::pdo()->query($requete);
        $resultat->setFetchMode(PDO::FETCH_CLASS, 'Role');
        return $resultat->fetchAll();
    }

    public static function getRoleById($id) {
        $requete = "SELECT * FROM Role WHERE IdRole = :idRole;";
        $stmt = connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idRole', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_CLASS, 'Role');
        return $stmt->fetch();
    }
}

?>


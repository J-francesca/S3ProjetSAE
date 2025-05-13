<?php


class Proposition {
    private $IdProp;
    private $TitreProp;
    private $DescriptProp;
    private $DateProp;
    private $FraisProp;
    private $IdMembre;
    private $IdTheme;
    private $VoteAuto;
	private $NbDemande;

    public function getIdProp() { return $this->IdProp; }
    public function getTitreProp() { return $this->TitreProp; }
    public function getDescriptProp() { return $this->DescriptProp; }
    public function getDateProp() { return $this->DateProp; }
    public function getFraisProp() { return $this->FraisProp; }
    public function getIdMembre() { return $this->IdMembre; }
    public function getIdTheme() { return $this->IdTheme; }
    public function getVoteAuto() { return $this->VoteAuto; }
	public function getNbDemande() { return $this->NbDemande; }

    public function setIdProp($idProp) { $this->IdProp = $idProp; }
    public function setTitreProp($titreProp) { $this->TitreProp = $itreProp; }
    public function setDescriptProp($descriptProp) { $this->DescriptProp = $descriptProp; }
    public function setDateProp($dateProp) { $this->DateProp = $dateProp; }
    public function setFraisProp($fraisProp) { $this->FraisProp = $fraisProp; }
    public function setIdMembre($idMembre) { $this->IdMembre = $idMembre; }
    public function setIdTheme($idTheme) { $this->IdTheme = $idTheme; }
    public function setVoteAuto($voteAuto) { $this->VoteAuto = $voteAuto; }
	public function setNbDemande($nbDemande) { $this->NbDemande = $nbDemande; }

    public function __construct($idProp = NULL, $titreProp = NULL, $descriptProp = NULL, $dateProp = NULL, $fraisProp = NULL, $idMembre = NULL, $idTheme = NULL, $voteAuto = NULL) {
        if (!is_null($idProp)) {
            $this->IdProp = $idProp;
            $this->TitreProp = $titreProp;
            $this->DescriptProp = $descriptProp;
            $this->DateProp = $dateProp;
            $this->FraisProp = $fraisProp;
            $this->IdMembre = $idMembre;
            $this->IdTheme = $idTheme;
            $this->VoteAuto = $voteAuto;
			$this->NbDemande = $nbDemande;
        }
    }

    public function afficher() {
        echo "<p>Titre proposition: $this->TitreProp, Description: $this->DescriptProp</p>";
    }
	
    public static function getAllPropositionsById($idGroupe) {
        try {
            $sql = "SELECT DISTINCT p.IdProp, p.TitreProp, p.FraisProp, p.IdTheme 
                    FROM Proposition p 
                    INNER JOIN Theme t ON p.IdTheme = t.IdTheme
                    INNER JOIN Groupe_Theme gt ON t.IdTheme = gt.IdTheme
                    WHERE gt.IdGroupe = :idGroupe
                    ORDER BY p.TitreProp ASC";
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Erreur dans la récupération des propositions: " . $e->getMessage());
            return [];
        }
    }


    
    public static function getAllPropositions() {
        $query = "SELECT * FROM Proposition";
        $stmt = Connexion::pdo()->query($query);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public static function getPropositionById($id) {
        $query = "SELECT IdProp,TitreProp, DescriptProp, DateProp, FraisProp, NomMembre, PrenomMembre
                  FROM Proposition p
                  JOIN Membre m ON p.IdMembre = m.IdMembre
                  WHERE p.IdProp = :idProp";
        $stmt = Connexion::pdo()->prepare($query);
        $stmt->bindParam(':idProp', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($result) {
            return $result; 
        } else {
            return false; 
        }
    }
	
	public static function getPropositionByIdProp($id) {
		$query = "SELECT *
				  FROM Proposition
				  WHERE IdProp = :idProp";
		$stmt = Connexion::pdo()->prepare($query);
		$stmt->bindParam(':idProp', $id, PDO::PARAM_INT);
		$stmt->execute();
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Proposition');
		$result = $stmt->fetch(); 
		
		if ($result) {
			return $result; 
		} else {
			return false; 
		}
	}
	
	public static function getPropositionByIdTheme($id) {
		$query = "SELECT *
				  FROM Proposition
				  WHERE IdTheme = :idTheme";
		$stmt = Connexion::pdo()->prepare($query);
		$stmt->bindParam(':idTheme', $id, PDO::PARAM_INT);
		$stmt->execute();
		
		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Proposition');
		$result = $stmt->fetch(); 
		
		if ($result) {
			return $result; 
		} else {
			return false; 
		}
	}

	public static function incrementNbDemande($idProp) {
    $query = "UPDATE Proposition SET NbDemande = NbDemande + 1 WHERE IdProp = :idProp";
    $stmt = Connexion::pdo()->prepare($query);
    $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);

    return $stmt->execute();
}
    
	public static function getAllPropositionsByidGroupe($idGroupe) {
		$requete = "SELECT p.IdProp, p.TitreProp, 
					p.DescriptProp, p.DateProp, 
					p.FraisProp, p.IdMembre, p.IdTheme, p.VoteAuto		   
					FROM Proposition p
					INNER JOIN Membre m ON m.IdMembre = p.IdMembre
					INNER JOIN Participation pt ON pt.IdMembre = m.IdMembre
					INNER JOIN Groupe_Theme gt ON gt.IdGroupe = pt.IdGroupe
					AND gt.IdTheme = p.IdTheme
					WHERE pt.IdGroupe = :idG";
					
		$stmt = connexion::pdo()->prepare($requete);
		$stmt->bindParam(':idG', $idGroupe, PDO::PARAM_INT);
		$stmt->execute();

		$stmt->setFetchMode(PDO::FETCH_CLASS, 'Proposition');
		$tableau = $stmt->fetchAll(); 
		return $tableau;
	}
	
    


    
	
	public function getProposant() {
    $requete = "
        SELECT m.nomMembre AS NomMembre, m.prenomMembre AS PrenomMembre
        FROM Proposition p
        INNER JOIN Membre m ON m.IdMembre = p.idMembre
        WHERE p.idProp = :idProp
    ";
    
    $stmt = connexion::pdo()->prepare($requete);
    $stmt->bindParam(':idProp', $this->IdProp, PDO::PARAM_INT);
    $stmt->execute();
    $resultat = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($resultat) {
        echo  $resultat['NomMembre'] . " " . $resultat['PrenomMembre'];
    } else {
        echo "<p>Aucun membre trouvé pour cette proposition.</p>";
    }
}

    
    

    public static function getStatsForProposition($idProp) {
        $query = "
            SELECT 
                (SELECT COUNT(*) FROM Reaction WHERE idProp = :idProp AND NomReact = 'Like') AS likes,
                (SELECT COUNT(*) FROM Reaction WHERE idProp = :idProp AND NomReact = 'Dislike') AS dislikes,
                (SELECT COUNT(*) FROM Commentaire WHERE idProp = :idProp) AS commentCount
        ";
        $stmt = Connexion::pdo()->prepare($query);
        $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        return [
            'likes' => $result['likes'] ?? 0,
            'dislikes' => $result['dislikes'] ?? 0,
            'commentCount' => $result['commentCount'] ?? 0
        ];
    }

    



    public static function updateLikeDislikeForProposition($idProp, $idMembre, $type) {
        $checkVoteQuery = "SELECT * FROM Reaction 
            WHERE IdMembre = :idMembre AND IdProp = :idProp";
        $stmt = Connexion::pdo()->prepare($checkVoteQuery);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
        $stmt->execute();
        $existingReaction = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($existingReaction) {
            if ($existingReaction['NomReact'] === $type) {
                $deleteQuery = "DELETE FROM Reaction 
                            WHERE IdMembre = :idMembre AND IdProp = :idProp";
                $stmt = Connexion::pdo()->prepare($deleteQuery);
                $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
                $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
                $stmt->execute();
    
                return ['success' => true, 'action' => 'removed'];
            } 
            else {
                $updateQuery = "UPDATE Reaction 
                            SET NomReact = :type, DateReact = NOW() 
                            WHERE IdMembre = :idMembre AND IdProp = :idProp";
                $stmt = Connexion::pdo()->prepare($updateQuery);
                $stmt->bindParam(':type', $type, PDO::PARAM_STR);
                $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
                $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
                $stmt->execute();
    
                return ['success' => true, 'action' => 'updated'];
            }
        }
    
        $insertReactionQuery = "INSERT INTO Reaction (NomReact, DateReact, IdMembre, IdProp) 
                    VALUES (:type, NOW(), :idMembre, :idProp)";
        $stmt = Connexion::pdo()->prepare($insertReactionQuery);
        $stmt->bindParam(':type', $type, PDO::PARAM_STR);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
        $stmt->execute();
    
        return ['success' => true, 'action' => 'added'];
    }

    
    public static function getUserReactionForProposition($idProp, $idMembre) {
        $query = "SELECT NomReact FROM Reaction 
                  WHERE IdMembre = :idMembre AND IdProp = :idProp";
        $stmt = Connexion::pdo()->prepare($query);
        $stmt->bindParam(':idMembre', $idMembre, PDO::PARAM_INT);
        $stmt->bindParam(':idProp', $idProp, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ? $result['NomReact'] : null;
    }







    public static function getIdGroupeFromProposition($IdProp) {
        $query = "
        SELECT Groupe.IdGroupe
        FROM Proposition
        INNER JOIN Theme ON Proposition.IdTheme = Theme.IdTheme
        INNER JOIN Groupe_Theme ON Theme.IdTheme = Groupe_Theme.IdTheme
        INNER JOIN Groupe ON Groupe_Theme.IdGroupe = Groupe.IdGroupe
        WHERE Proposition.IdProp = :IdProp
    ";
    
        $stmt = connexion::pdo()->prepare($query);
        $stmt->bindParam(':IdProp', $IdProp, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchColumn();
    }
    




    public static function requestFormalVote($idProp, $idMembre, $idGroupe) {
        $checkVoteQuery = "SELECT IdVote FROM Vote WHERE IdVote = :idProp";
        $stmt = connexion::pdo()->prepare($checkVoteQuery);
        $stmt->execute([':idProp' => $idProp]);
        $voteExists = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if (!$voteExists) {
            $_SESSION['message'] = 'La proposition n\'existe pas.';
            $_SESSION['message_type'] = 'error';
            header("Location: " . $_SERVER['HTTP_REFERER']);
            exit();
        }
    
        $checkQuery = "SELECT Resultat FROM ResultatVote 
                      WHERE IdMembre = :idMembre AND IdVote = :idProp";
        $stmt = connexion::pdo()->prepare($checkQuery);
        $stmt->execute([
            ':idMembre' => $idMembre,
            ':idProp' => $idProp
        ]);
        $existingVote = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($existingVote) {
            if ($existingVote['Resultat'] == 2) {
                $_SESSION['message'] = 'Vous avez déjà demandé un vote formel pour cette proposition.';
                $_SESSION['message_type'] = 'warning';
            }
        } else {
            $query = "INSERT INTO ResultatVote (IdMembre, IdVote, Resultat) 
                     VALUES (:idMembre, :idProp, 2)";
            $stmt = connexion::pdo()->prepare($query);
            $stmt->execute([
                ':idMembre' => $idMembre,
                ':idProp' => $idProp
            ]);
            $_SESSION['message'] = 'Demande de vote enregistrée. En attente de la majorité.';
            $_SESSION['message_type'] = 'info';
        }
    
        $checkMajorityQuery = "
            SELECT 
                COUNT(DISTINCT rv.IdMembre) AS demandes,
                (SELECT COUNT(*) FROM Participation WHERE IdGroupe = :idGroupe) AS total_membres
            FROM ResultatVote rv
            WHERE rv.IdVote = :idProp AND rv.Resultat = 2
        ";
        $stmt = connexion::pdo()->prepare($checkMajorityQuery);
        $stmt->execute([
            ':idProp' => $idProp,
            ':idGroupe' => $idGroupe
        ]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
    
        if ($result['demandes'] > ($result['total_membres'] / 2)) {
            $query = "UPDATE ResultatVote 
                     SET Resultat = NULL 
                     WHERE IdVote = :idProp AND Resultat = 2";
            $stmt = connexion::pdo()->prepare($query);
            $stmt->execute([':idProp' => $idProp]);
    
            $voteResult = Vote::createVoteForProposition($idProp, $idGroupe);
            $_SESSION['message'] = $voteResult ? 'Vote déclenché avec succès.' : 'Erreur lors du déclenchement du vote.';
            $_SESSION['message_type'] = $voteResult ? 'success' : 'error';
        }
    
        header("Location: " . $_SERVER['HTTP_REFERER']);
        exit();
    }
    


    public static function ajouterProposition($titre, $descript, $frais, $idMembre, $idTheme, $voteAuto) {
    try {
        $pdo = connexion::pdo();
        $pdo->beginTransaction();  

        $requete = "INSERT INTO Proposition (TitreProp, DescriptProp, DateProp, FraisProp, IdMembre, IdTheme, VoteAuto) 
                    VALUES (?, ?, CURDATE(), ?, ?, ?, ?)";
        $stmt = $pdo->prepare($requete);

        $stmt->execute([
            $titre,
            $descript,
            $frais,
            $idMembre,
			$idTheme,
            $voteAuto
        ]);

        $idProposition = $pdo->lastInsertId();


        $pdo->commit();
        
        error_log("Proposition ajoutée avec succès. ID: " . $idProposition);

        return $idProposition; 

    } catch (PDOException $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log("Erreur PDO lors de l'ajout de la proposition : " . $e->getMessage());

        $_SESSION['error'] = "Une erreur est survenue lors de l'ajout de la proposition. Veuillez réessayer.";

        return false;
        
    } catch (Exception $e) {
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }

        error_log("Erreur générale lors de l'ajout de la proposition : " . $e->getMessage());

        $_SESSION['error'] = $e->getMessage();

        return false;
    }
}

		



	public static function supprimerProposition($idProp) {
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


	/*_____________________JSON___________________*/


    public static function getPropositionsGroupesParTheme($idGroupe) {
        $requete = "
            SELECT 
                p.IdProp,
                p.TitreProp,
                p.FraisProp,
                t.IdTheme,
                t.NomTheme,
                gt.BudgetTheme,
                COUNT(DISTINCT rv.IdMembre) as nbVotesTotal,
                SUM(CASE WHEN rv.Resultat = 1 THEN 1 ELSE 0 END) as nbVotesSatisfaits
            FROM Theme t
            LEFT JOIN Groupe_Theme gt ON gt.IdTheme = t.IdTheme AND gt.IdGroupe = :idGroupe
            LEFT JOIN Proposition p ON p.IdTheme = t.IdTheme 
            LEFT JOIN Vote v ON v.IdProp = p.IdProp
            LEFT JOIN ResultatVote rv ON rv.IdVote = v.IdVote
            WHERE gt.IdGroupe = :idGroupe
            GROUP BY 
                p.IdProp,
                p.TitreProp,
                p.FraisProp,
                t.IdTheme,
                t.NomTheme,
                gt.BudgetTheme
            ORDER BY t.NomTheme, p.TitreProp";
    
        error_log("Executing query for group: " . $idGroupe);
    
        $stmt = Connexion::pdo()->prepare($requete);
        $stmt->bindParam(':idGroupe', $idGroupe, PDO::PARAM_INT);
        $stmt->execute();
        $resultats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
        error_log("Query results: " . print_r($resultats, true));
    
        if (empty($resultats)) {
            error_log("No results found for group: " . $idGroupe);
            return ['error' => 'Aucune proposition trouvée pour ce groupe'];
        }
    
        $groupesParTheme = [];
        foreach ($resultats as $row) {
            if (empty($row['IdProp'])) {
                continue;
            }
    
            $theme = $row['NomTheme'];
    
            if (!isset($groupesParTheme[$theme])) {
                $groupesParTheme[$theme] = [
                    'IdTheme' => $row['IdTheme'],
                    'BudgetTheme' => $row['BudgetTheme'],
                    'Propositions' => []
                ];
            }
    
            $proposition = [
                'IdProp' => $row['IdProp'],
                'TitreProp' => $row['TitreProp'],
                'FraisProp' => $row['FraisProp'],
                'nbVotesTotal' => (int)$row['nbVotesTotal'],
                'nbVotesSatisfaits' => (int)$row['nbVotesSatisfaits']
            ];
    
            $groupesParTheme[$theme]['Propositions'][] = $proposition;
        }
    
        error_log("Grouped results: " . print_r($groupesParTheme, true));
    
        return $groupesParTheme;
    }


/*

public static function bruteForce($propositions, $resultats, $budgetAnnuel) {
    if (empty($propositions) || empty($resultats) || $budgetAnnuel <= 0) {
        throw new Exception("Paramètres invalides pour l'algorithme.");
    }

    $bestSolution = null; 
    $minCost = PHP_FLOAT_MAX;

  
    $users = [];
    foreach ($resultats as $resultat) {
        if ($resultat['Resultat'] == 1) {
            $users[$resultat['IdMembre']] = true;
        }
    }
    $users = array_keys($users); 

    $numProposals = count($propositions);
    $numCombinations = 1 << $numProposals; 

    for ($i = 1; $i < $numCombinations; $i++) {
        $currentSelection = [];
        $currentCost = 0;

        for ($j = 0; $j < $numProposals; $j++) {
            if (($i & (1 << $j)) != 0) { 
                $currentSelection[] = $propositions[$j];
                $currentCost += floatval($propositions[$j]->getFraisProp());
            }
        }

        if ($currentCost > $budgetAnnuel) {
            continue;
        }

        $satisfiesAllUsers = true;
        foreach ($users as $userId) {
            $hasSupported = false;
            foreach ($resultats as $resultat) {
                if ($resultat['IdMembre'] == $userId && $resultat['Resultat'] == 1) {
                    foreach ($currentSelection as $proposal) {
                        if ($proposal->getIdProp() == $resultat['IdProp']) {
                            $hasSupported = true;
                            break;
                        }
                    }
                    if ($hasSupported) break;
                }
            }
            if (!$hasSupported) {
                $satisfiesAllUsers = false;
                break;
            }
        }

        if ($satisfiesAllUsers && $currentCost < $minCost) {
            $minCost = $currentCost;
            $bestSolution = $currentSelection;
        }
    }

    if ($bestSolution === null) {
        throw new Exception("Aucune solution valide n'a été trouvée dans le budget.");
    }

    return $bestSolution; 
*/

}
?>

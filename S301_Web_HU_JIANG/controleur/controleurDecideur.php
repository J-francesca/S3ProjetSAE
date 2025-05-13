<?php
require_once("modele/proposition.php");
require_once("modele/groupe.php"); 
require_once("modele/resultatvote.php"); 

class ControleurDecideur {
    public static function afficherPropositions() {
        if (!isset($_GET['idGroupe'])) {
            if (isset($_GET['format']) && $_GET['format'] === 'json') {
                header('Content-Type: application/json');
                echo json_encode(['error' => 'L\'ID du groupe est manquant.']);
            } else {
                echo "Erreur : L'ID du groupe est manquant.";
            }
            return;
        }

        $idGroupe = intval($_GET['idGroupe']);
        $mode = isset($_GET['mode']) ? $_GET['mode'] : 'normal';
        $format = isset($_GET['format']) ? $_GET['format'] : 'html';

        try {
            if ($format === 'json') {
                header('Content-Type: application/json');

                if ($mode === 'minBudget') {
                    try {
                        $budgetAnnuel = Groupe::getBudgetAnnuelbyId($idGroupe);
                        $propositions = Proposition::getAllPropositionsById($idGroupe);
                        $resultats = ResultatVote::getAllResultats($idGroupe);

                        if (empty($propositions) || empty($resultats) || !$budgetAnnuel) {
                            throw new Exception("Données insuffisantes pour l'optimisation");
                        }

                        $selectedProposals = Proposition::bruteForce($propositions, $resultats, $budgetAnnuel);

                        echo json_encode([
                            'mode' => $mode,
                            'data' => $selectedProposals
                        ]);

                    } catch (Exception $e) {
                        echo json_encode(['error' => $e->getMessage()]);
                    }
                } else {
                    $propositionsParTheme = Proposition::getPropositionsGroupesParTheme($idGroupe);
                    echo json_encode([
                        'mode' => $mode,
                        'data' => $propositionsParTheme
                    ]);
                }
            } else {
                $titre = "Décisions pour le groupe";
                $Style = "StyleDecision";
                $propositionsParTheme = $mode === 'minBudget' 
                    ? Proposition::bruteForce($propositions, $resultats, $budgetAnnuel)
                    : Proposition::getPropositionsGroupesParTheme($idGroupe);
                include("./vue/debut.php");
                include("./vue/membre/pageDecision.php");
                include("./vue/fin.html");
            }
        } catch (Exception $e) {
            if ($format === 'json') {
                header('Content-Type: application/json');
                echo json_encode(['error' => $e->getMessage()]);
            } else {
                echo "Erreur : " . $e->getMessage();
            }
        }
    }

    public static function selectionner() {
        $data = json_decode(file_get_contents('php://input'), true);

        if (!isset($data['idProposition']) || !isset($data['idGroupe'])) {
            echo json_encode(['error' => 'Données manquantes']);
            return;
        }

        try {
            Proposition::selectionnerProposition($data['idProposition'], $data['idGroupe']);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
        }
    }
}
?>
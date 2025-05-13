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
            error_log("Mode: " . $mode);
            error_log("Format: " . $format);

            if ($format === 'json') {
                header('Content-Type: application/json');

            if ($mode === 'minBudget') {
                try {
                    $budgetAnnuel = Groupe::getBudgetAnnuelbyId($idGroupe);
                    error_log("Budget annuel: " . $budgetAnnuel);

                    $propositions = Proposition::getAllPropositionsById($idGroupe);
                    error_log("Propositions: " . print_r($propositions, true));

                    $resultats = ResultatVote::getAllResultats($idGroupe);
                    error_log("Résultats votes: " . print_r($resultats, true));

                    $javaInput = [
                        'propositions' => array_map(function($prop) {
                            return [
                                'id' => intval($prop['IdProp']),
                                'title' => $prop['TitreProp'],
                                'cost' => floatval($prop['FraisProp']),
                                'themeId' => intval($prop['IdTheme'])
                            ];
                        }, $propositions),
                        'votes' => array_map(function($vote) {
                            return [
                                'userId' => intval($vote['idMembre']),
                                'proposalId' => intval($vote['idProp']),
                                'support' => $vote['resultat'] == 1
                            ];
                        }, $resultats),
                        'budget' => floatval($budgetAnnuel),
                        'mode' => $mode
                    ];

                    error_log("Java input: " . json_encode($javaInput));



                    $javaCmd = 'java -cp ' . __DIR__ . '/../lib/*:' . __DIR__ . '/../classes teste.BudgetOptimizer';
                    error_log("Executing command: " . $javaCmd);

                    $jsonInput = json_encode($javaInput);
                    error_log("Input JSON: " . $jsonInput);

                    if (json_last_error() !== JSON_ERROR_NONE) {
                        error_log("JSON encode error: " . json_last_error_msg());
                        throw new Exception("Error encoding JSON data");
                    }

                    $descriptorspec = array(
                        0 => array("pipe", "r"),
                        1 => array("pipe", "w"),
                        2 => array("pipe", "w")
                    );

                    $process = proc_open($javaCmd, $descriptorspec, $pipes);

                    if (is_resource($process)) {
                        fwrite($pipes[0], json_encode($javaInput));
                        fclose($pipes[0]);

                        $output = stream_get_contents($pipes[1]);
                        error_log("Java output: " . $output);
                        fclose($pipes[1]);

                        // Lire erreurs
                        $errors = stream_get_contents($pipes[2]);
                        error_log("Java errors: " . $errors);
                        fclose($pipes[2]);

                        $return_value = proc_close($process);
                        error_log("Java return value: " . $return_value);

                        if ($return_value !== 0) {
                            throw new Exception("Erreur d'exécution Java: " . $errors);
                        }

                        $selectedProposals = json_decode($output, true);
                        error_log("Selected proposals: " . print_r($selectedProposals, true));

                        if ($format === 'json') {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'mode' => $mode,
                                'data' => $selectedProposals
                            ]);
                        } else {
                            $titre = "Décisions pour le groupe";
                            $Style = "StyleDecision";
                            $propositionsParTheme = $selectedProposals; // Utiliser les résultats traités par Java
                            include("./vue/debut.php");
                            include("./vue/membre/pageDecision.php");
                            include("./vue/fin.html");
                        }
                    } else {
                        throw new Exception("Impossible de lancer le programme Java");
                    }
                } catch (Exception $e) {
                    error_log("Error in minBudget mode: " . $e->getMessage());
                    if ($format === 'json') {
                        header('Content-Type: application/json');
                        echo json_encode(['error' => $e->getMessage()]);
                    } else {
                        echo "Erreur : " . $e->getMessage();
                    }
                }
            }
            } else {
                // Mode normal
                $propositionsParTheme = Proposition::getPropositionsGroupesParTheme($idGroupe);

                if ($format === 'json') {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'mode' => $mode,
                        'data' => $propositionsParTheme
                    ]);
                } else {
                    $titre = "Décisions pour le groupe";
                    $Style = "StyleDecision";
                    include("./vue/debut.php");
                    include("./vue/membre/pageDecision.php");
                    include("./vue/fin.html");
                }
            }
        } catch (Exception $e) {
            error_log("Error: " . $e->getMessage());
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


<?php
require_once("modele/proposition.php");
require_once("modele/commentaire.php");
require_once("modele/signalement.php");

class ControleurSignalement {


    public static function ajouterSignalementComm() {
        error_log('Contenu de $_POST pour commentaire : ' . print_r($_POST, true));
        
        $idComm = intval($_POST['idComm']);
        $idMembre = intval($_POST['idMembre']);
        $raison = strip_tags($_POST['raisonSignal']);
        $dateSignal = isset($_POST['dateSignal']) ? $_POST['dateSignal'] : null;
        
        $signalement = new Signalement(
            null, 
            $dateSignal,
            $raison, 
            $idMembre,
            null,
            $idComm
        );
        
        $result = Signalement::ajouterSignalementComm($signalement);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Signalement ajouté avec succès'
        ]);
        exit;
    }
    
    public static function ajouterSignalementProp() {
        error_log('Contenu de $_POST pour proposition : ' . print_r($_POST, true));
        
        $idProp = intval($_POST['idProp']);
        $idMembre = intval($_POST['idMembre']);
        $raison = strip_tags($_POST['raisonSignal']);
        $dateSignal = isset($_POST['dateSignal']) ? $_POST['dateSignal'] : null;
        
        $signalement = new Signalement(
            null, 
            $dateSignal,
            $raison, 
            $idMembre,
            $idProp,
            null
        );
        
        $result = Signalement::ajouterSignalementProp($signalement);
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'message' => 'Signalement ajouté avec succès'
        ]);
        exit;
    }



    public static function ignoreSignalement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idSignal'])) {
            $idSignal = intval($_POST['idSignal']);
            
            error_log("Tentative d'ignorer le signalement : " . $idSignal);  
            
            try {
                $resultat = Signalement::supprimerSignalement($idSignal);
                
                error_log("Résultat de la suppression : " . ($resultat ? "succès" : "échec")); 
                
                header('Content-Type: application/json');
                if ($resultat) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Erreur lors de la suppression']);
                }
            } catch (Exception $e) {
                error_log("Erreur lors de la suppression : " . $e->getMessage());  
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
        }
    }
    
    public static function supprimerCommentaireEtSignalement() {
        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['idComm'])) {
                throw new Exception('Invalid request parameters');
            }
            
            $idComm = intval($_POST['idComm']);
            error_log("Attempting to delete comment ID: " . $idComm);
            
            $resultSignalement = Signalement::supprimerSignalementParIdComm($idComm);
            $resultCommentaire = Commentaire::supprimerCommentaire($idComm);
            
            if (!$resultSignalement || !$resultCommentaire) {
                throw new Exception('Failed to delete comment or its reports');
            }
            
            header('Content-Type: application/json');
            echo json_encode(['success' => true]);
            exit;
            
        } catch (Exception $e) {
            error_log("Error in supprimerCommentaireEtSignalement: " . $e->getMessage());
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    public static function supprimerPropositionEtSignalement() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['idProp'])) {
            $idProp = intval($_POST['idProp']);
            
            try {
                error_log("Tentative de suppression de la proposition ID: " . $idProp);
                
                $resultSignalement = Signalement::supprimerSignalementParIdProp($idProp);
                $resultProposition = Proposition::supprimerProposition($idProp);
                
                header('Content-Type: application/json');
                if ($resultSignalement && $resultProposition) {
                    echo json_encode([
                        'success' => true, 
                        'message' => 'Proposition et signalement supprimés avec succès'
                    ]);
                } else {
                    echo json_encode([
                        'success' => false, 
                        'message' => 'Erreur lors de la suppression'
                    ]);
                }
            } catch (Exception $e) {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $e->getMessage()]);
            }
            exit;
        }
    }



    public static function listeSignalement() {
        $titre = "Gestion des Signalements";
        $Style = "StyleSignalement"; 

        $idGroupe = isset($_GET['idG']) ? intval($_GET['idG']) : 0;  
        $type = isset($_GET['type']) ? $_GET['type'] : 'all';

        if ($type == 'commentaire') {
            $signalements = Signalement::getSignalementByType('commentaire');
        } elseif ($type == 'proposition') {
            $signalements = Signalement::getSignalementByType('proposition');
        } else {      
        $signalements = Signalement::getSignalementByIdGroupe($idGroupe);
        }
        include("./vue/debut.php");
        include("./vue/pageDeDiscussion/pageSignalement.php");
        include("./vue/fin.html");
    }


}

?>

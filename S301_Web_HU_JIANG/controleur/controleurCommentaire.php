<?php

require_once("modele/commentaire.php");
    
class ControleurCommentaire {
    
        public static function updateLikeDislikeC() {
            if (!isset($_GET['idMembre']) || !is_numeric($_GET['idMembre'])) {
                echo "ID de membre manquant ou invalide.";
                exit;
            }
        
            if (!isset($_GET['idComm'], $_GET['type']) || !is_numeric($_GET['idComm'])) {
                echo "Paramètres manquants ou invalides.";
                exit;
            }
        
            $idMembre = intval($_GET['idMembre']);
            $idComm = intval($_GET['idComm']);
            $type = $_GET['type'];  // 'Like' ou 'Dislike'
        
            $result = Commentaire::updateLikeDislikeForCommentaire($idComm, $idMembre, $type);
        
            if ($result['success']) {
                header("Location: routeur.php?controleur=proposition&action=lireProposition&id={$_GET['idProp']}&idMembre={$idMembre}");
            } else {
                header("Location:routeur.php?controleur=proposition&action=lireProposition&id={$_GET['idProp']}&idMembre={$idMembre}&error=" . urlencode($result['message']));
            }
            exit;
         

    }



    public static function AjouterCommentaire() {
        try {
            if (!isset($_POST['IdProp'], $_POST['commentaire'], $_POST['IdMembre']) || empty(trim($_POST['commentaire']))) {
                header("Location: proposition.php?id={$_POST['IdProp']}&error=missing_data");
                exit;
            }
    
            $idMembre = intval($_POST['IdMembre']); 
            $idProp = intval($_POST['IdProp']);
            $commentaire = trim($_POST['commentaire']);
    
            $result = Commentaire::ajouterCommentaire($idMembre, $idProp, $commentaire);
    
            if ($result['success']) {
                header("Location: routeur.php?controleur=proposition&action=lireProposition&id={$idProp}&idMembre={$idMembre}");
            } else {
                header("Location: routeur.php?controleur=proposition&action=lireProposition&id={$idProp}&idMembre={$idMembre}&error=" . urlencode($result['message']));
            }
            exit;
        } catch (Exception $e) {
            header("Location: erreur.php");
            exit;
        }
    }
    






    
    }



    

?>

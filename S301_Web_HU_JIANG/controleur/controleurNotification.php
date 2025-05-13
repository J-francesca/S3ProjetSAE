<?php

require_once("./modele/notification.php");


class ControleurNotification {
    public static function afficherNotifications() {
        if (!isset($_COOKIE['user_id'])) {
            header('Location: routeur.php?controleur=membre&action=afficherFormulaireConnexion');
            exit();
        }
        
        $idMembre = isset($_GET['id']) ? intval($_GET['id']) : $_COOKIE['user_id'];
        

        $notifications = Notification::getNotifications($idMembre);
error_log(print_r($notifications, true)); 
        
        $titre = "Notifications";
        $Style = "StyleNotification";
        include("./vue/debut.php");
        include("./vue/notification/listeNotif.php");
        include("./vue/fin.html");
    }
    
    public static function marquerCommeLue() {
        if (!isset($_COOKIE['user_id'])) {
            header('Location: routeur.php?controleur=membre&action=afficherFormulaireConnexion');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['notification_id'])) {
            $idMembre = $_COOKIE['user_id'];
            $notificationId = $_POST['notification_id'];
            
            header('Location: routeur.php?controleur=notification&action=afficherNotifications');
            exit();
        }
    }
}
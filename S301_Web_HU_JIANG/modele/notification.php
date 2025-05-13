<?php



class Notification {


    public static function getNotifications($idMembre) {
        $notifications = [];
        $vuesNotifications = isset($_COOKIE['vues_notifications']) ? 
            json_decode($_COOKIE['vues_notifications'], true) : [];
        
        $datesEntreeGroupes = self::getDatesEntreeGroupes($idMembre);

        self::getCommentNotifications($idMembre, $notifications, $vuesNotifications);

        self::getInvitationsRecues($idMembre, $notifications, $vuesNotifications);
        self::getInvitationsEnvoyees($idMembre, $notifications, $vuesNotifications);
        self::getActiviteGroupes($idMembre, $notifications, $vuesNotifications, $datesEntreeGroupes);
        self::getPropositionsGroupes($idMembre, $notifications, $vuesNotifications, $datesEntreeGroupes);
        self::getRoleChanges($idMembre, $notifications, $vuesNotifications);
        
        usort($notifications, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });

        return $notifications;
    }
    
    private static function getCommentNotifications($idMembre, &$notifications, $vuesNotifications) {
        $sql = "SELECT n.IdNotification, n.Message, n.DateNotification, 
                p.TitreProp as PropositionTitre, m.NomMembre, g.NomGroupe
                FROM Notification n
                JOIN Proposition p ON p.IdProp = n.IdProposition
                JOIN Commentaire c ON c.IdComm = n.IdCommentaire
                JOIN Membre m ON m.IdMembre = c.IdMembre
                JOIN Theme t ON t.IdTheme = p.IdTheme
                JOIN Groupe_Theme gt ON gt.IdTheme = t.IdTheme
                JOIN Groupe g ON g.IdGroupe = gt.IdGroupe
                WHERE n.IdMembre = ? AND n.EstLue = FALSE
                ORDER BY n.DateNotification DESC";
        
        try {
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->execute([$idMembre]);
            
            while ($row = $stmt->fetch()) {
                $notifId = 'comment_' . $row['IdNotification'];
                
                if (!in_array($notifId, $vuesNotifications)) {
                    $notifications[] = [
                        'id' => $notifId,
                        'message' => $row['NomMembre'] . " a commenté votre proposition '" . 
                                   $row['PropositionTitre'] . "' dans le groupe " . $row['NomGroupe'],
                        'date' => $row['DateNotification']
                    ];
                }
            }
        } catch (PDOException $e) {
            error_log("Erreur dans getCommentNotifications: " . $e->getMessage());
        }
    }




    private static function getDatesEntreeGroupes($idMembre) {
        $dates = [];
        $sql = "SELECT IdGroupe, DateInscriptionMembre
                FROM Participation p
                JOIN Membre m ON p.IdMembre=m.IdMembre
                WHERE IdMembre = ?";
    
        try {
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->execute([$idMembre]);
    
            while ($row = $stmt->fetch()) {
                $dates[$row['IdGroupe']] = $row['DateInscriptionMembre'];
                error_log("Date d'entrée pour le groupe " . $row['IdGroupe'] . ": " . $row['DateInscriptionMembre']);
            }
        } catch (PDOException $e) {
            error_log("Erreur dans getDatesEntreeGroupes: " . $e->getMessage());
        }
    
        return $dates;
    }
    



    private static function getPropositionsGroupes($idMembre, &$notifications, $vuesNotifications, $datesEntreeGroupes) {
        $sql = "SELECT DISTINCT p.IdProp, g.NomGroupe, g.IdGroupe, m.NomMembre, p.DateProp, mem.DateInscriptionMembre
                FROM Proposition p
                JOIN Theme t ON t.IdTheme = p.IdTheme
                JOIN Groupe_Theme gt ON gt.IdTheme = t.IdTheme
                JOIN Groupe g ON g.IdGroupe = gt.IdGroupe
                JOIN Membre m ON m.IdMembre = p.IdMembre
                JOIN Participation part ON part.IdGroupe = g.IdGroupe
                JOIN Membre mem ON mem.IdMembre = part.IdMembre
                WHERE part.IdMembre = ?
                AND p.IdMembre != ?
                AND p.DateProp >= mem.DateInscriptionMembre
                ORDER BY p.DateProp DESC
                LIMIT 10";
        
        try {
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->execute([$idMembre, $idMembre]);
            
            while ($row = $stmt->fetch()) {
                $notifId = 'proposition_' . $row['IdProp'];
                
                if (!in_array($notifId, $vuesNotifications)) {
                    $notifications[] = [
                        'id' => $notifId,
                        'message' => "Nouvelle proposition dans le groupe " . $row['NomGroupe'] . " par " . $row['NomMembre'],
                        'date' => $row['DateProp']
                    ];
                }
            }
        } catch (PDOException $e) {
            error_log("Erreur dans getPropositionsGroupes: " . $e->getMessage());
        }
    }



    public static function getRoleChanges($idMembre, &$notifications, $vuesNotifications) {
        $sql = "SELECT g.NomGroupe,  p.IdGroupe,  m.DateInscriptionMembre 
                FROM Participation p
                JOIN Membre m ON m.IdMembre = p.IdMembre
                JOIN Groupe g ON g.IdGroupe = p.IdGroupe
                WHERE p.IdMembre = ?
                LIMIT 5";

        try {
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->execute([$idMembre]);

            while ($row = $stmt->fetch()) {
                $notifId = 'role_change_' . $row['IdGroupe'];

                if (!in_array($notifId, $vuesNotifications)) {
                    $notifications[] = [
                        'id' => $notifId,
                        'message' => "Votre rôle dans le groupe " . $row['NomGroupe'] . " a été modifié",
                        'date' => $row['DateInscriptionMembre']

                    ];
                }
            }
        } catch (PDOException $e) {
            error_log("Erreur dans getRoleChanges: " . $e->getMessage());
        }
    }
    
    



    
private static function getActiviteGroupes($idMembre, &$notifications, $vuesNotifications, $datesEntreeGroupes) {
    $sql = "SELECT DISTINCT g.NomGroupe, m.NomMembre, p1.IdGroupe, m.DateInscriptionMembre 
            FROM Participation p1
            JOIN Groupe g ON g.IdGroupe = p1.IdGroupe
            JOIN Membre m ON m.IdMembre = p1.IdMembre
            JOIN Participation p2 ON p2.IdGroupe = p1.IdGroupe
            WHERE p2.IdMembre = ?
            AND p1.IdMembre != ?
            AND p1.IdRole = 7
            ORDER BY m.DateInscriptionMembre DESC
            LIMIT 5";

    try {
        $stmt = connexion::pdo()->prepare($sql);
        $stmt->execute([$idMembre, $idMembre]);

        $results = $stmt->fetchAll();
        if (empty($results)) {
            error_log("Aucune donnée retournée dans getActiviteGroupes.");
        }

        foreach ($results as $row) {
            error_log("Traitement du groupe: " . $row['NomGroupe'] . " avec le membre: " . $row['NomMembre']);

            if (isset($datesEntreeGroupes[$row['IdGroupe']])) {
                $notifId = 'activite_' . $row['IdGroupe'] . '_' . $row['NomMembre'];
                if (!in_array($notifId, $vuesNotifications)) {
                    $notifications[] = [
                        'id' => $notifId,
                        'message' => $row['NomMembre'] . " a rejoint le groupe " . $row['NomGroupe'],
                        'date' => $row['DateInscriptionMembre']

                    ];
                    error_log("Notification ajoutée: " . $row['NomMembre'] . " a rejoint " . $row['NomGroupe']);
                    
                }
            }
        }
    } catch (PDOException $e) {
        error_log("Erreur dans getActiviteGroupes: " . $e->getMessage());
    }
}






private static function getInvitationsRecues($idMembre, &$notifications, $vuesNotifications) {
    $sql = "SELECT DISTINCT g.NomGroupe, p1.IdGroupe , m.DateInscriptionMembre 
            FROM Participation p1 
            JOIN Groupe g ON g.IdGroupe = p1.IdGroupe 
            WHERE p1.IdMembre != ? 
            AND p1.IdRole IN (1, 2) 
            AND EXISTS (
                SELECT 1 
                FROM Participation p2 
                WHERE p2.IdMembre = ? 
                AND p2.IdGroupe = p1.IdGroupe 
                AND p2.IdRole = 7  
            )
            ORDER BY p1.IdGroupe DESC 
            LIMIT 10";
    
    try {
        $stmt = connexion::pdo()->prepare($sql);
        $stmt->execute([$idMembre, $idMembre]);
        
        while ($row = $stmt->fetch()) {
            $notifId = 'invite_' . $row['IdGroupe'];
            
            if (!in_array($notifId, $vuesNotifications)) {
                $notifications[] = [
                    'id' => $notifId,
                    'message' => "Vous avez été invité au groupe " . $row['NomGroupe'],
                    'date' => $row['DateInscriptionMembre']

                ];
            }
        }
    } catch (PDOException $e) {
        error_log("Error in getInvitationsRecues: " . $e->getMessage());
    }
}


    
    private static function getInvitationsEnvoyees($idMembre, &$notifications, $vuesNotifications) {
        $sql = "SELECT m.NomMembre, g.NomGroupe, p1.IdGroupe, m.DateInscriptionMembre 
                FROM Participation p1 
                JOIN Groupe g ON g.IdGroupe = p1.IdGroupe 
                JOIN Membre m ON m.IdMembre = p1.IdMembre 
                WHERE EXISTS (
                    SELECT 1 
                    FROM Participation p2 
                    WHERE p2.IdMembre = ? 
                    AND p2.IdGroupe = p1.IdGroupe 
                    AND p2.IdRole IN (1, 2)  
                )
                AND p1.IdRole = 7  
                AND p1.IdMembre != ? 
                ORDER BY p1.IdGroupe DESC 
                LIMIT 10";
        
        try {
            $stmt = connexion::pdo()->prepare($sql);
            $stmt->execute([$idMembre, $idMembre]);
            
            while ($row = $stmt->fetch()) {
                $notifId = 'envoi_' . $row['IdGroupe'];
                
                if (!in_array($notifId, $vuesNotifications)) {
                    $notifications[] = [
                        'id' => $notifId,
                        'message' => "Vous avez invité " . $row['NomMembre'] . " au groupe " . $row['NomGroupe'],
                        'date' => $row['DateInscriptionMembre']

                    ];
                }
            }
        } catch (PDOException $e) {
            error_log("Error in getInvitationsEnvoyees: " . $e->getMessage());
        }
    }





}
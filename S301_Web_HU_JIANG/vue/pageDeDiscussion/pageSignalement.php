<div class="back-button">
    <button onclick="window.history.back()">
        <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/retour.png" alt="Retour" class="icon-arrow">
    </button>
</div>
 
    <div class="signalements-container">
        <h1>Gestion des Signalements</h1>

        <div class="filters">
            <form method="GET" action="">
                <input type="hidden" name="controleur" value="signalement">
                <input type="hidden" name="action" value="listeSignalement">
                <input type="hidden" name="idG" value="<?= $idGroupe ?>">
                <select name="type">
                    <option value="all" <?= isset($_GET['type']) && $_GET['type'] === 'all' ? 'selected' : '' ?>>Tous les types</option>
                    <option value="proposition" <?= isset($_GET['type']) && $_GET['type'] === 'proposition' ? 'selected' : '' ?>>Propositions</option>
                    <option value="commentaire" <?= isset($_GET['type']) && $_GET['type'] === 'commentaire' ? 'selected' : '' ?>>Commentaires</option>
                </select>
                <button type="submit">Filtrer</button>
            </form>
        </div>


        <?php if (empty($signalements)): ?>
            <div class="empty-state">
                <h3>Aucun signalement trouvé</h3>
                <p>Il n'y a actuellement aucun signalement à vérifier.</p>
            </div>
        <?php else: ?>
            <?php foreach ($signalements as $signalement): ?>
            <div class="signalement-card">
                <div class="signalement-header">
                    <div class="signalement-date">
                        Signalé le : <?= htmlspecialchars($signalement->getDateSignal()) ?>
                    </div>
                    <div>
                        ID: #<?= htmlspecialchars($signalement->getIdSignal()) ?>
                    </div>
                </div>

                <div class="signalement-raison">
                    <strong>Raison du signalement :</strong>
                    <p><?= htmlspecialchars($signalement->getRaisonSignal()) ?></p>
                </div>




                <div class="signalement-details">
                <?php if ($signalement->getIdProp()): ?>
                    <?php 
                        $proposition = Proposition::getPropositionById($signalement->getIdProp());
                        if ($proposition) {
                            $descriptProp = htmlspecialchars($proposition['DescriptProp']);
                        }
                    ?>
                <div class="detail-item">
                    <strong>Proposition signalée :</strong>
                    <p><?= isset($descriptProp) ? $descriptProp : 'Pas de description disponible.' ?></p>
                    <div class="button-group">
                        <button class="btn-supprimer" data-id="prop_<?= htmlspecialchars($signalement->getIdProp()) ?>" onclick="supprimerSignalement(this)">Supprimer proposition</button>
                        <button class="btn-ignorer" data-signal="<?= htmlspecialchars($signalement->getIdSignal()) ?>" onclick="ignorerSignalement(this)">Ignorer signalement</button>
                    </div>
                </div>
                <?php endif; ?>



                <?php if ($signalement->getIdComm()): ?>
                    <?php
                        $commentaire = Commentaire::getCommentaireById($signalement->getIdComm());
                        if ($commentaire) {
                            $contenuComm = htmlspecialchars($commentaire['ContenuComm']);
                        } 
                    ?>
                <div class="detail-item">
                    <strong>Commentaire signalé :</strong>
                    <p><?= isset($contenuComm) ? $contenuComm : 'Pas de contenu disponible.' ?></p>
                    <div class="button-group">
                        <button class="btn-supprimer" data-id="<?= $signalement->getIdComm() ? 'comm_'.htmlspecialchars($signalement->getIdComm()) : 'prop_'.htmlspecialchars($signalement->getIdProp()) ?>" onclick="supprimerSignalement(this)">Supprimer commentaire</button>
                        <button class="btn-ignorer" data-signal="<?= htmlspecialchars($signalement->getIdSignal()) ?>" onclick="ignorerSignalement(this)"> Ignorer signalement</button>
                    </div>
                </div>
                <?php endif; ?>


                </div>
            </div>
        <?php endforeach; ?>
        <?php endif; ?>

    <?php if (isset($_SESSION['message'])): ?>
        <div class="alert">
            <?= htmlspecialchars($_SESSION['message']) ?>
        </div>
        <?php unset($_SESSION['message']); ?>
    <?php endif; ?>





<script>
function supprimerSignalement(button) {
    var confirmation = confirm("Êtes-vous sûr de vouloir supprimer cet élément ?");
    
    if (confirmation) {
        var id = button.getAttribute('data-id');
        var type = id.split('_')[0];
        var idElement = id.split('_')[1];
        
        console.log("Type:", type, "ID:", idElement);
        
        var url = type === 'prop' 
            ? 'routeur.php?controleur=signalement&action=supprimerPropositionEtSignalement' 
            : 'routeur.php?controleur=signalement&action=supprimerCommentaireEtSignalement';
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', url, true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
            console.log("Réponse reçue:", xhr.responseText); 
            
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        var card = button.closest('.signalement-card');
                        if (card) {
                            card.remove();
                            alert('Élément supprimé avec succès');
                        }
                    } else {
                        alert(response.message || 'Une erreur est survenue');
                    }
                } catch (e) {
                    console.error("Erreur parsing JSON:", e);
                    alert('Erreur lors du traitement de la réponse');
                }
            }
        };
        
        var data = type === 'prop' ? 'idProp=' + idElement : 'idComm=' + idElement;
        xhr.send(data);
    }
}

function ignorerSignalement(button) {
    var confirmation = confirm("Êtes-vous sûr de vouloir ignorer ce signalement ?");
    
    if (confirmation) {
        var idSignal = button.getAttribute('data-signal');
        console.log("ID du signalement à ignorer:", idSignal); 
        
        var xhr = new XMLHttpRequest();
        xhr.open('POST', 'routeur.php?controleur=signalement&action=ignoreSignalement', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        
        xhr.onload = function() {
            console.log("Réponse reçue:", xhr.responseText); 
            
            if (xhr.status === 200) {
                try {
                    var response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        button.closest('.signalement-card').remove();
                        alert('Signalement ignoré avec succès');
                    } else {
                        alert(response.message || 'Une erreur est survenue');
                    }
                } catch (e) {
                    console.error("Erreur lors du parsing JSON:", e); 
                    alert('Erreur lors du traitement de la réponse');
                }
            }
        };
        
        xhr.send('idSignal=' + idSignal);
    }
}

</script>

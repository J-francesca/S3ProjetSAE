<div class="container">
        <div class="back-button">
        <button onclick="window.location.href = 'routeur.php?controleur=discussion&action=lireDiscussionAvecPropositions&id=<?php echo urlencode($idMembre); ?>&idGroupe=<?php echo urlencode($idGroupe); ?>';">
            <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/arrows.png" alt="Retour" class="icon-arrow">
        </button>
    </div>


        <div class="author-info">
            <p><strong>Proposé par :</strong> <?php echo htmlspecialchars($proposition['NomMembre']) . ' ' . htmlspecialchars($proposition['PrenomMembre']); ?></p>
        </div>






<div class="proposition-header" oncontextmenu="openSignalPropMenu(event, <?php echo $proposition['IdProp']; ?>)">
    <div class="signal-panel" id="signalPropPanel">
        <h3>Raison du signalement de la proposition</h3>
        <textarea id="reasonProp" placeholder="Entrez la raison du signalement"></textarea>
        <button onclick="signalProposition()">Signaler</button>
        <button onclick="closeSignalPropPanel()">Annuler</button>
    </div>


    <?php if ($canDelete && !empty($propositionSignalee)): ?>
        <a href="routeur.php?controleur=proposition&action=supprimerProposition&idProp=<?php echo $proposition['IdProp']; ?>&idMembre=<?php echo $idMembre; ?>" class="delete-button">
            <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/supprimer.png" alt="Supprimer" class="icon-delete">
        </a>
    <?php endif; ?>

    <h1><?php echo htmlspecialchars($proposition['TitreProp']); ?></h1>

    <div class="argumentation">
        <p><strong>Description de la proposition :</strong> <?php echo htmlspecialchars($proposition['DescriptProp']); ?></p>
    </div>

    <div class="details">
        <span><strong>Frais associés :</strong> <?php echo htmlspecialchars($proposition['FraisProp']); ?> €</span>
        <span><strong>Date de publication :</strong> <?php echo htmlspecialchars($proposition['DateProp']); ?></span>
        
    </div>

    <div class="metrics">
        <?php 
            $userReaction = Proposition::getUserReactionForProposition($proposition['IdProp'], $_GET['idMembre']);
                
            $likeButtonClass = ($userReaction === 'Like') ? 'like-button active' : 'like-button';
            $dislikeButtonClass = ($userReaction === 'Dislike') ? 'dislike-button active' : 'dislike-button';
        ?>

                
        <a href="routeur.php?controleur=proposition&action=updateLikeDislikeP&idProp=<?php echo $proposition['IdProp']; ?>&type=Like&idMembre=<?php echo $_GET['idMembre']; ?>" class="<?php echo $likeButtonClass; ?>">
            ❤️ <?php echo htmlspecialchars($stats['likes']); ?> Likes
        </a>
        <a href="routeur.php?controleur=proposition&action=updateLikeDislikeP&idProp=<?php echo $proposition['IdProp']; ?>&type=Dislike&idMembre=<?php echo $_GET['idMembre']; ?>" class="<?php echo $dislikeButtonClass; ?>">
             💔 <?php echo htmlspecialchars($stats['dislikes']); ?> Dislikes
        </a>
        <span>💬 <?php echo htmlspecialchars($stats['commentCount']); ?> Commentaires</span>
    </div>
</div>




    
<div class="comments">
    <h2>Commentaires</h2>
    <ul>
        <?php if (!empty($commentaires)): ?>
            <?php foreach ($commentaires as $commentaire): ?>
                <li class="commentaire-container" oncontextmenu="openSignalMenu(event, <?php echo $commentaire['IdComm']; ?>)">
                    <div class="commentaire-header">
                        <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/photoprofil.png" alt="Photo de profil" class="photo-profil">
                        <span class="commentaire-name">
                            <?php echo htmlspecialchars($commentaire['NomMembre'] ?? 'Non trouvé') . ' ' . htmlspecialchars($commentaire['PrenomMembre'] ?? ''); ?>
                        </span>
                    </div>
                    
                    <div class="commentaire-body">
                        <?php echo htmlspecialchars($commentaire['ContenuComm'] ?? 'Commentaire non disponible'); ?>
                    </div>
                    
                    <div class="commentaire-stats">
                        <?php
                            $userReaction = Commentaire::getUserReactionForCommentaire($commentaire['IdComm'], $_GET['idMembre']);
                            $likeButtonClass = ($userReaction === 'Like') ? 'like-button active' : 'like-button';
                            $dislikeButtonClass = ($userReaction === 'Dislike') ? 'dislike-button active' : 'dislike-button';
                        ?>
                        
                        <a href="routeur.php?controleur=commentaire&action=updateLikeDislikeC&idComm=<?php echo $commentaire['IdComm']; ?>&type=Like&idProp=<?php echo $proposition['IdProp']; ?>&idMembre=<?php echo $_GET['idMembre']; ?>" class="<?php echo $likeButtonClass; ?>">
                            ❤️ <?php echo htmlspecialchars($commentaire['LikeCount'] ?? 0); ?>
                        </a>
                        <a href="routeur.php?controleur=commentaire&action=updateLikeDislikeC&idComm=<?php echo $commentaire['IdComm']; ?>&type=Dislike&idProp=<?php echo $proposition['IdProp']; ?>&idMembre=<?php echo $_GET['idMembre']; ?>" class="<?php echo $dislikeButtonClass; ?>">
                            💔 <?php echo htmlspecialchars($commentaire['DislikeCount'] ?? 0); ?>
                        </a>
                        
                        <?php if ($canDelete && !empty($commentaire['EstSignale'])): ?>
                            <a href="routeur.php?controleur=commentaire&action=supprimerCommentaire&idComm=<?php echo $commentaire['IdComm']; ?>&idMembre=<?php echo $idMembre; ?>&idProp=<?php echo $proposition['IdProp']; ?>" class="delete-button">
                                <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/supprimer.png" alt="Supprimer" class="icon-delete">
                            </a>
                        <?php endif; ?>
                    </div>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li>Aucun commentaire pour cette proposition.</li>
        <?php endif; ?>
    </ul>
</div>




<div class="signal-panel" id="signalPanel">
    <h3>Raison du signalement du commentaire</h3>
    <textarea id="reason" placeholder="Entrez la raison du signalement"></textarea>
    <button onclick="signalComment()">Signaler</button>
    <button onclick="closeSignalPanel()">Annuler</button>
</div>




<div class="signal-feedback" id="signalFeedback"></div>




<?php
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type']; 
    echo "<div class='alert alert-$message_type'>$message</div>";

    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>


<div class="vote-section">
    <?php 
    $voteExists = $voteExists ?? false;
    $voteStatus = $voteStatus ?? '';
    ?>

    <?php if ($voteStatus === 'terminer' || $voteStatus === null || !$voteExists): ?>
        <?php if ($voteInfo['canOrganizeVote']): ?>
            <a href="routeur.php?controleur=proposition&action=creerVote&id=<?php echo $idProp; ?>&idMembre=<?php echo $idMembre; ?>"
            class="btn btn-danger">Organiser un vote</a>
        <?php elseif ($voteInfo['canRequestVote']): ?>
            
			<p>Demande de vote enregistrée. En attente de la majorité.</p>
				
        <?php else: ?>
            <p>Aucun vote n'est possible pour le moment</p>
        <?php endif; ?>
        <?php elseif ($voteExists && $voteStatus === 'en cours'): ?>
			<?php 
				$vote = Vote::getVoteByIdProp($idProp);
				$idVote = $vote->getIdVote(); 
				?>
			<?php
			// Vérifier si une demande de vote formelle existe déjà pour cette proposition et ce membre
			if (Vote::existeDemandeVote($idVote, $idMembre)): ?>
				<p>Demande de vote formelle déjà enregistrée. En attente de la majorité.</p>
			<?php else: ?>
				<p>Un vote est en cours :</p>
				<form action="routeur.php?controleur=proposition&action=demandeVote&id=<?php echo $idProp; ?>&idMembre=<?php echo $idMembre; ?>" method="POST">
					
					<input type="hidden" name="IdProp" value="<?php echo htmlspecialchars($proposition['IdProp']); ?>">
					<input type="hidden" name="IdVote" value="<?php echo $idVote; ?>">
					<button type="submit" name="vote" value="oui">✅ Oui</button>
					<button type="submit" name="vote" value="non">❌ Non</button>
				</form>
			<?php endif; ?>
		<?php endif; ?>
</div>




<div class="add-comment">
    <form action="routeur.php?controleur=commentaire&action=ajouterCommentaire" method="POST">
        <input type="hidden" name="IdProp" value="<?php echo htmlspecialchars($proposition['IdProp']); ?>">
        <input type="hidden" name="IdMembre" value="<?php echo htmlspecialchars($_GET['idMembre'] ?? ''); ?>">
        <textarea name="commentaire" placeholder="Ajoutez un commentaire..." required></textarea>
        <button type="submit">Poster</button>
    </form>
</div>

</div>




<script>


function openSignalMenu(event, IdComm) {
    event.preventDefault(); 

    var signalPanel = document.getElementById('signalPanel');
    

    signalPanel.style.display = 'block';
    
    signalPanel.style.position = 'absolute';
    signalPanel.style.left = event.pageX + 'px';
    signalPanel.style.top = event.pageY + 'px';

    signalPanel.setAttribute('data-comment-id', IdComm);
}

function closeSignalPanel() {
    var signalPanel = document.getElementById('signalPanel');
    signalPanel.style.display = 'none';
}

function signalComment() {
    var signalPanel = document.getElementById('signalPanel');
    var commentId = signalPanel.getAttribute('data-comment-id');
    var reasonElement = document.getElementById('reason');
    var reason = reasonElement.value.trim();
    
    var urlParams = new URLSearchParams(window.location.search);
    var idMembre = urlParams.get('idMembre');
    
    if (reason === "") {
        alert("Veuillez entrer une raison pour signaler le commentaire.");
        return;
    }

    var currentDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'routeur.php?controleur=signalement&action=ajouterSignalementComm', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    var params = 'idComm=' + encodeURIComponent(commentId) + 
                '&raisonSignal=' + encodeURIComponent(reason) + 
                '&idMembre=' + encodeURIComponent(idMembre) + 
                '&dateSignal=' + encodeURIComponent(currentDate);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert(response.message);
                    closeSignalPanel();
                    reasonElement.value = ''; 
                } else {
                    alert('Erreur: ' + (response.message || 'Une erreur est survenue'));
                }
            } catch (e) {
                console.error("Erreur de parsing JSON:", e);
                console.log("Réponse brute:", xhr.responseText);
                alert('Une erreur est survenue lors du traitement de la réponse');
            }
        }
    };

    xhr.send(params);
}











function openSignalPropMenu(event, IdProp) {
    event.preventDefault(); 

    var signalPropPanel = document.getElementById('signalPropPanel');
    
    signalPropPanel.style.display = 'block';
    signalPropPanel.style.position = 'absolute';
    signalPropPanel.style.left = event.pageX + 'px';
    signalPropPanel.style.top = event.pageY + 'px';

    signalPropPanel.setAttribute('data-prop-id', IdProp);
}

function closeSignalPropPanel() {
    var signalPropPanel = document.getElementById('signalPropPanel');
    signalPropPanel.style.display = 'none';
}

function signalProposition() {
    var signalPropPanel = document.getElementById('signalPropPanel');
    var propId = signalPropPanel.getAttribute('data-prop-id');
    var reasonElement = document.getElementById('reasonProp');
    var reason = reasonElement.value.trim();
    
    var urlParams = new URLSearchParams(window.location.search);
    var idMembre = urlParams.get('idMembre');
    
    if (reason === "") {
        alert("Veuillez entrer une raison pour signaler la proposition.");
        return;
    }

    var currentDate = new Date().toISOString().slice(0, 19).replace('T', ' ');
    
    var xhr = new XMLHttpRequest();
    xhr.open('POST', 'routeur.php?controleur=signalement&action=ajouterSignalementProp', true);
    xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');

    var params = 'idProp=' + encodeURIComponent(propId) + 
                '&raisonSignal=' + encodeURIComponent(reason) + 
                '&idMembre=' + encodeURIComponent(idMembre) + 
                '&dateSignal=' + encodeURIComponent(currentDate);

    xhr.onreadystatechange = function() {
        if (xhr.readyState === XMLHttpRequest.DONE) {
            try {
                var response = JSON.parse(xhr.responseText);
                if (response.success) {
                    alert(response.message);
                    closeSignalPropPanel();
                    reasonElement.value = ''; 
                } else {
                    alert('Erreur: ' + (response.message || 'Une erreur est survenue'));
                }
            } catch (e) {
                console.error("Erreur de parsing JSON:", e);
                console.log("Réponse brute:", xhr.responseText);
                alert('Une erreur est survenue lors du traitement de la réponse');
            }
        }
    };

    xhr.send(params);
}




function showFeedback(message) {
    const feedback = document.getElementById('signalFeedback');
    feedback.textContent = message;
    feedback.style.display = 'block';
    setTimeout(() => {
        feedback.style.display = 'none';
    }, 3000);
}


</script>
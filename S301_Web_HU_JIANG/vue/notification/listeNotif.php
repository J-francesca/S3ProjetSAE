

<div class="back-button">
    <button onclick="window.history.back()">
        <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/retour.png" alt="Retour" class="icon-arrow">
    </button>
</div>


<div class="notifications-container">
    <h1>Notifications</h1>
    
    <?php if (empty($notifications)): ?>
        <div class="notification-empty">
            Aucune notification à afficher
        </div>
    <?php else: ?>
        <?php foreach ($notifications as $notif): ?>
            <div class="notification-item" data-notification-id="<?php echo htmlspecialchars($notif['id']); ?>">
                <?php echo htmlspecialchars($notif['message']); ?>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div id="confirmDialog" class="confirm-dialog">
    <div class="confirm-content">
        <p>Voulez-vous supprimer cette notification ?</p>
        <div class="confirm-buttons">
            <button id="confirmYes">Confirmer</button>
            <button id="confirmNo">Annuler</button>
        </div>
    </div>
</div>

<style>
    body {
    background-color: #f5ecde;
    margin: 0;
    padding: 0;
    min-height: 100vh;
}
.notifications-container {
    max-width: 800px;
    margin: 20px auto;
    padding: 20px;
}

.notification-item {
    background: #fff;
    border: 1px solid #e0e0e0;
    border-radius: 4px;
    margin-bottom: 10px;
    padding: 15px;
    cursor: pointer;
    transition: background-color 0.2s;
}

.notification-item:hover {
    background-color: #f2d5a9;
}

.notification-empty {
    text-align: center;
    padding: 20px;
    background: #f2d5a9;
    border-radius: 4px;
}

.confirm-dialog {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    z-index: 1000;
}

.confirm-content {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    background: white;
    padding: 20px;
    border-radius: 8px;
    text-align: center;
}

.confirm-buttons {
    margin-top: 20px;
}

.confirm-buttons button {
    margin: 0 10px;
    padding: 8px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
}

#confirmYes {
    background: #eaa643;
    color: white;
}

#confirmNo {
    background: #eaa643;
    color: white;
}


.back-button {
    position: fixed;
    top: 20px; 
    left: 20px; 
    z-index: 10; 
}

.back-button button {
    background-color: transparent;
    border: none;
    padding: 10px;
    cursor: pointer;
}

.back-button button:hover {
    background-color: #e79317; 
    border-radius: 5px;
}

.icon-arrow {
    width: 30px; 
    height: 30px; 
}
 

footer {
    position: fixed;
    bottom: 0;
    width: 100%;
    background-color:transparent;
    text-align: center;
    padding: 10px 0;
}

</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const confirmDialog = document.getElementById('confirmDialog');
    const confirmYes = document.getElementById('confirmYes');
    const confirmNo = document.getElementById('confirmNo');
    let currentNotification = null;

    document.querySelectorAll('.notification-item').forEach(item => {
        item.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            currentNotification = this;
            confirmDialog.style.display = 'block';
        });
    });

    confirmYes.addEventListener('click', function() {
        if (currentNotification) {
            markAsRead(currentNotification);
        }
        confirmDialog.style.display = 'none';
    });

    confirmNo.addEventListener('click', function() {
        confirmDialog.style.display = 'none';
    });

    function markAsRead(notification) {
        const notifId = notification.dataset.notificationId;
        let vuesNotifications = [];
        try {
            vuesNotifications = JSON.parse(getCookie('vues_notifications') || '[]');
        } catch (e) {
            vuesNotifications = [];
        }
        
        if (!vuesNotifications.includes(notifId)) {
            vuesNotifications.push(notifId);
        }
        
        document.cookie = `vues_notifications=${JSON.stringify(vuesNotifications)};max-age=2592000;path=/`;
        
        notification.style.display = 'none';
        
        if (document.querySelectorAll('.notification-item:not([style*="display: none"])').length === 0) {
            const emptyMessage = document.createElement('div');
            emptyMessage.className = 'notification-empty';
            emptyMessage.textContent = 'Aucune notification à afficher';
            document.querySelector('.notifications-container').appendChild(emptyMessage);
        }
    }

    function getCookie(name) {
        const value = `; ${document.cookie}`;
        const parts = value.split(`; ${name}=`);
        if (parts.length === 2) return parts.pop().split(';').shift();
    }
});
</script>

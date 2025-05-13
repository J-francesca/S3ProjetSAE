<form method="POST" action="routeur.php?controleur=membre&action=traiterModification" enctype="multipart/form-data">
    <div class="back-button">
        <button onclick="window.history.back()">
            <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/retour.png" alt="Retour" class="icon-arrow">
        </button>
    </div>
    <input type="hidden" name="idMembre" value="<?php echo isset($_GET['id']) ? $_GET['id'] : ''; ?>">    

    <div class="form-group">
        <label for="nom">Nom</label>
        <input type="text" id="nom" name="nom" value="<?php echo $membre->getNomMembre(); ?>" required>
        </div>

    <div class="form-group">
        <label for="prenom">Prénom</label>
        <input type="text" id="prenom" name="prenom" value="<?php echo $membre->getPrenomMembre(); ?>" required>
        </div>

    <div class="form-group">
        <label for="adresse">Adresse</label>
        <input type="text" id="adresse" name="adresse" value="<?php echo $membre->getAdresseMembre(); ?>">
        </div>

    <div class="form-group">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo $membre->getEmailMembre(); ?>">
        </div>

    <div class="form-group">
        <label for="tel">Téléphone</label>
        <input type="tel" id="tel" name="tel" value="<?php echo $membre->getTelMembre(); ?>">
        </div>

    <div class="form-group">
        <label for="dateNaissance">Date de naissance</label>
        <input type="date" id="dateNaissance" name="dateNaissance" value="<?php echo $membre->getDateNaissanceMembre(); ?>">
    </div>

    <div class="form-group">
            <label for="photo">Photo de profil</label>
            <div class="photo-upload-container">
                <div class="photo-preview">
                    <img 
                        id="profilePicturePreview"
                        class="profile-picture"
                        src="<?php echo !empty($membre->getPhoto()) ? './source/photoprofil/' . $membre->getPhoto() : './source/imageGroupe.png'; ?>" 
                        alt="Photo de profil">
                </div>
                <input 
                    type="file" 
                    id="photo" 
                    name="photo" 
                    accept="image/*" 
                    class="photo-input" 
                    onchange="previewProfilePicture(event)">
            </div>
        </div>
    <div class="form-group">
        <label for="nouveauMotDePasse">Nouveau mot de passe</label>
        <input type="password" id="nouveauMotDePasse" name="nouveauMotDePasse">
    </div>

    <div class="form-group">
        <label for="confirmationMotDePasse">Confirmer le mot de passe</label>
        <input type="password" id="confirmationMotDePasse" name="confirmationMotDePasse">
    </div>

    <button type="submit">Modifier</button>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const photoInput = document.getElementById('photo');
    const photoPreview = document.getElementById('photoPreview');

    photoInput.addEventListener('change', function(event) {
        if (event.target.files && event.target.files[0]) {
            const reader = new FileReader();
            
            reader.onload = function(e) {
                photoPreview.src = e.target.result;
            }
            
            reader.readAsDataURL(event.target.files[0]);
        }
    });
});


        function previewProfilePicture(event) {
            const preview = document.getElementById('profilePicturePreview');
            const file = event.target.files[0];

            if (file) {
                const reader = new FileReader();

                reader.onload = function(e) {
                    preview.src = e.target.result;
                };

                reader.readAsDataURL(file);
            } else {
                preview.src = '<?php echo isset($currentUser['photo']) ? $currentUser['photo'] : 'default-avatar.png'; ?>';
            }
        }


</script>

 <div class="back-button">
    <button onclick="window.history.back()">
        <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/retour.png" alt="Retour" class="icon-arrow">
    </button>
</div>
 
 
 <div class="container">

        <h1>Créer un nouveau groupe</h1>

        <?php if (isset($_GET['error'])): ?>
        <div class="error-message">
            <?php echo htmlspecialchars($_GET['error']); ?>
        </div>
        <?php endif; ?>

        

        <form method="POST" action="routeur.php?controleur=groupe&action=creerGroupe" enctype="multipart/form-data">
        <input type="hidden" name="idMembre" value="<?php echo htmlspecialchars($_GET['idMembre'] ?? ''); ?>">
            <div class="form-group">
                <label for="nomGroupe">Nom du groupe</label>
                <input type="text" id="nomGroupe" name="nomGroupe" required>
            </div>
            
            <div class="form-group">
                <label for="couleurGroupe">Couleur du groupe</label>
                <div>
                    <?php
                        $couleursDisponibles = [
                            '#cedaf2', '#f9d1c5', '#c2dc5b', '#ffff4d'
                        ];

                    foreach ($couleursDisponibles as $couleur) {
                        echo "<label style=\"background-color: $couleur; padding: 10px; margin: 5px;\">";
                        echo "<input type=\"radio\" name=\"couleurGroupe\" value=\"$couleur\" required> ";
                        echo "</label>";
                    }
                    ?>
                </div>
            </div>

            
            <div class="form-group">
                <label for="budgetGroupe">Budget initial</label>
                <input type="number" id="budgetGroupe" name="budgetGroupe" min="0" step="0.01" required>
            </div>

            <div class="form-group">
                <label for="imageGroupe">Image du groupe</label>
                <input type="file" id="imageGroupe" name="imageGroupe" accept="image/*">
            </div>

<div class="form-group">
            <label>Thèmes et leurs budgets</label>
            <div class="themes-container" id="themesContainer">
                <div class="theme-item">
                    <input type="text" name="themes[]" placeholder="Nom du thème" required>
                    <input type="number" name="themeBudgets[]" placeholder="Budget du thème" step="0.01" min="0" required>
                    <button type="button" onclick="addTheme()">+</button>
                </div>
            </div>
        </div>
        
        <button type="submit" class="btn-submit">Créer le groupe</button>
    </form>
</div>


<script>
function addTheme() {
    const container = document.getElementById('themesContainer');
    const newTheme = document.createElement('div');
    newTheme.className = 'theme-item';
    newTheme.innerHTML = `
        <input type="text" name="themes[]" placeholder="Ajouter un thème" required>
        <input type="number" name="themeBudgets[]" placeholder="Budget du thème" step="0.01" min="0" required>
        <button type="button" onclick="this.parentElement.remove()">-</button>
    `;
    container.appendChild(newTheme);
}


document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('imageGroupe');
    const previewContainer = document.createElement('div');
    const previewImage = document.createElement('img');
    
    previewContainer.className = 'image-preview-container';
    previewImage.className = 'preview-image';
    previewImage.src = './source/imageGroupe.png';
    
    imageInput.parentNode.appendChild(previewContainer);
    previewContainer.appendChild(previewImage);
    
    imageInput.addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
            };
            reader.readAsDataURL(file);
        } else {
            previewImage.src = './source/imageGroupe.png';
        }
    });
});


</script>
<div class="back-button">
    <button onclick="window.history.back()">
        <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/retour.png" alt="Retour" class="icon-arrow">
    </button>
</div>


<div class="decideur-container">
    <h1 class="decideur-header">Décideur</h1>
    
    <div class="mode-buttons">
        <a href="?controleur=decideur&action=afficherPropositionsParGroupe&idGroupe=<?= $_GET['idGroupe'] ?>&mode=normal" 
           class="mode-option <?= ($_GET['mode'] ?? 'normal') === 'normal' ? 'active' : '' ?>">
            Voir toutes les propositions
        </a>
        <a href="?controleur=decideur&action=afficherPropositionsParGroupe&idGroupe=<?= $_GET['idGroupe'] ?>&mode=minBudget" 
           class="mode-option <?= ($_GET['mode'] ?? '') === 'minBudget' ? 'active' : '' ?>">
            Minimiser le budget avec votes satisfaits
        </a>
    </div>

    <div class="theme-section">
        <?php foreach ($propositionsParTheme as $theme => $propositions): ?>
            <div class="theme-header">
                <?= htmlspecialchars($theme) ?> 
                <?php if (isset($propositions[0]['budget_theme'])): ?>
                    (Budget: <?= number_format($propositions[0]['budget_theme'], 2) ?> €)
                <?php endif; ?>
            </div>
            <table class="propositions-table">
                <thead>
                    <tr>
                        <th>Proposition</th>
                        <th>Votes satisfaits</th>
                        <th>Coût</th>
                        <?php if (($_GET['mode'] ?? '') === 'minBudget'): ?>
                            <th>Action</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($propositions as $prop): ?>
                        <tr>
                            <td><?= htmlspecialchars($prop['titreProp']) ?></td>
                            <td><?= $prop['nbVotesSatisfaits'] ?> / <?= $prop['nbVotesTotal'] ?></td>
                            <td><?= number_format($prop['frais'], 2) ?> €</td>
                            <?php if (($_GET['mode'] ?? '') === 'minBudget'): ?>
                                <td>
                                    <button onclick="selectionnerProposition(<?= $prop['IdProp'] ?>)" class="btn-select">
                                        Sélectionner
                                    </button>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endforeach; ?>
    </div>
</div>

<style>
.mode-option {
    display: inline-block;
    padding: 10px 20px;
    margin: 10px;
    background-color: #f0f0f0;
    border-radius: 5px;
    text-decoration: none;
    color: #333;
}

.mode-option.active {
    background-color: #007bff;
    color: white;
}

.btn-select {
    padding: 5px 10px;
    background-color: #28a745;
    color: white;
    border: none;
    border-radius: 3px;
    cursor: pointer;
}

.btn-select:hover {
    background-color: #218838;
}
</style>


        <div class="budget-section">
            <div class="budget-info">
                <span>Budget total:</span>
                <button class="modify-button">Modifier</button>
            </div>
            <div class="budget-info">
                <span>Budget total restant:</span>
                <span>xxxxx €</span>
            </div>
        </div>

        <button class="confirm-button">Confirmer le choix</button>

        
        
        
    <div class="theme-selector">


    <span>Thème</span>
    <select id="theme-select">
        <option value="">Sélectionnez un thème</option>
    </select>
    <span>, avec budget :</span>
    <input type="number" id="budget-input" value="" placeholder="Entrez le budget">
    <button class="modify-button" onclick="modifierBudget()">Modifier budget</button>
</div>

<script>
function loadPropositions(mode) {
    const idGroupe = new URLSearchParams(window.location.search).get('idGroupe');
    window.location.href = `routeur.php?controleur=decideur&action=afficherPropositionsParGroupe&idGroupe=${idGroupe}&mode=${mode}`;
}

function selectionnerProposition(idProp) {
    alert(`Proposition ${idProp} sélectionnée`);
}


/*
    // Fonction pour charger les thèmes et leurs budgets dans le select
    document.addEventListener('DOMContentLoaded', function () {
        fetch('getThemes.php')  // Cette page récupérera les thèmes et leurs budgets
            .then(response => response.json())
            .then(data => {
                let select = document.getElementById('theme-select');
                data.forEach(theme => {
                    let option = document.createElement('option');
                    option.value = theme.IdTheme;
                    option.textContent = theme.NomTheme + ' - Budget: ' + theme.BudgetTheme + ' €';
                    select.appendChild(option);
                });
            });
    });

    function modifierBudget() {
        const themeId = document.getElementById('theme-select').value;
        const nouveauBudget = document.getElementById('budget-input').value;

        if (!themeId || !nouveauBudget) {
            alert('Veuillez sélectionner un thème et entrer un budget.');
            return;
        }

        // Envoi de la requête AJAX pour mettre à jour le budget
        const xhr = new XMLHttpRequest();
        xhr.open('POST', 'modifierBudget.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.onload = function () {
            if (xhr.status === 200) {
                alert('Budget mis à jour avec succès.');
            } else {
                alert('Erreur lors de la mise à jour du budget.');
            }
        };
        xhr.send('idTheme=' + themeId + '&budget=' + nouveauBudget);
    }

*/
    
/*

const JAVA_API_URL = 'http://localhost:8080/api/decision';

class DecisionAPI {
    static async calculateMinBudgetWithVotes(themeId) {
        const response = await fetch('routeur.php?controleur=decideur&action=getMinBudgetVotes');        if (!response.ok) throw new Error('Failed to calculate min budget with votes');
        return await response.json();
    }
}

document.querySelector('[data-mode="min-budget"]').addEventListener('click', async function () {
    try {
        const response = await fetch('/routeur.php?controleur=decideur&action=runMinBudgetAlgo', {
            method: 'POST',
        });
        const data = await response.json();
        displayResultsByTheme(data);
    } catch (error) {
        console.error('Error:', error);
        alert('Erreur lors du calcul');
    }
});


function displayResultsByTheme(results) {
    const container = document.querySelector('.theme-section');
    container.innerHTML = '';
    
    for (const [theme, propositions] of Object.entries(results)) {
        const themeHtml = `
            <div class="theme-header">
                ${theme.titreTheme} (budget: ${theme.budgetTheme}€)
            </div>
            <table class="propositions-table">
                <thead>
                    <tr>
                        <th>Proposition</th>
                        <th>Votes satisfaits</th>
                        <th>Frais</th>
                    </tr>
                </thead>
                <tbody>
                    ${propositions.map(prop => `
                        <tr>
                            <td>${prop.titreProp}</td>
                            <td>${prop.nbSupport} / ${prop.nbVote}</td>
                            <td>${prop.fraisProp}€</td>
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        `;
        container.innerHTML += themeHtml;
    }
}*/

</script>
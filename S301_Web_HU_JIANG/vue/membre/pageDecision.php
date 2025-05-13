<div class="back-button">
        <button onclick="window.history.back()">
            <img src="https://projets.iut-orsay.fr/saes3-shu/S301_Web_HU_JIANG/source/retour.png" alt="Retour" class="icon-arrow">
        </button>
    </div>

    <div class="decideur-container">
        <h1 class="decideur-header">Décideur</h1>

        <div class="mode-buttons">
            <button id="normalModeBtn" class="mode-option active">
                Liste des propositions
            </button>
            <button id="minBudgetModeBtn" class="mode-option">
                Minimiser le budget avec votes satisfaits
            </button>
        </div>

        <div id="propositions-container" class="theme-section">
        </div>
    </div>

    <style>
        .decideur-container {
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .mode-buttons {
            margin: 20px 0;
            text-align: center;
        }

        .mode-option {
            padding: 10px 20px;
            margin: 0 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            background-color: #f0f0f0;
            transition: all 0.3s ease;
        }

        .mode-option.active {
            background-color: #007bff;
            color: white;
        }

        .theme-section {
            margin-top: 30px;
        }

        .theme-header {
            background-color: #f8f9fa;
            padding: 15px;
            margin-bottom: 15px;
            font-weight: bold;
            border-radius: 5px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .budget-info {
            color: #666;
            font-size: 0.9em;
        }

        .propositions-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }

        .propositions-table th,
        .propositions-table td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }

        .propositions-table th {
            background-color: #f5f5f5;
            font-weight: 600;
        }

        .propositions-table tr:hover {
            background-color: #f8f9fa;
        }

        .btn-select {
            padding: 6px 15px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .btn-select:hover {
            background-color: #218838;
        }

        .loading {
            text-align: center;
            padding: 20px;
            font-style: italic;
            color: #666;
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
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const idGroupe = urlParams.get('idGroupe') || 1;
    const mode = 'normal';
    const format = 'json';
    
    loadPropositions(idGroupe, mode, format);

    document.getElementById('normalModeBtn').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('minBudgetModeBtn').classList.remove('active');
        loadPropositions(idGroupe, 'normal', format);
    });

    document.getElementById('minBudgetModeBtn').addEventListener('click', function() {
        this.classList.add('active');
        document.getElementById('normalModeBtn').classList.remove('active');
        loadPropositions(idGroupe, 'minBudget', format);
    });
});

function loadPropositions(idGroupe, mode, format) {
    const container = document.getElementById('propositions-container');
    container.innerHTML = '<div class="loading">Chargement des propositions...</div>';

    fetch(`routeur.php?controleur=decideur&action=afficherPropositions&idGroupe=${idGroupe}&mode=${mode}&format=${format}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }

            container.innerHTML = '';

            if (mode === 'minBudget') {
                let totalCost = 0;
                const resultsDiv = document.createElement('div');
                resultsDiv.className = 'optimized-results';

                const summaryDiv = document.createElement('div');
                summaryDiv.className = 'summary-card';
                summaryDiv.innerHTML = '<h2>Résultats de l\'optimisation</h2>';
                container.appendChild(summaryDiv);

                const tableDiv = document.createElement('div');
                tableDiv.innerHTML = `
                    <table class="propositions-table">
                        <thead>
                            <tr>
                                <th>Titre</th>
                                <th>Coût</th>
                                <th>Thème</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                `;

                const tbody = tableDiv.querySelector('tbody');
                data.data.forEach(prop => {
                    totalCost += parseFloat(prop.FraisProp);
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${prop.TitreProp}</td>
                        <td>${parseFloat(prop.FraisProp).toFixed(2)} €</td>
                        <td>${prop.IdTheme}</td>
                    `;
                    tbody.appendChild(row);
                });

                summaryDiv.innerHTML += `
                    <p>Coût total: ${totalCost.toFixed(2)} €</p>
                    <p>Nombre de propositions: ${data.data.length}</p>
                `;

                container.appendChild(tableDiv);
            } else {
                Object.entries(data.data).forEach(([theme, themeData]) => {
                    if (theme === 'mode') return;
                    
                    const themeSection = document.createElement('div');
                    themeSection.className = 'theme-section';
                    
                    const themeHeader = document.createElement('div');
                    themeHeader.className = 'theme-header';
                    themeHeader.innerHTML = `
                        <h2 class="theme-title">${theme}</h2>
                        <p class="theme-budget">Budget: ${themeData.BudgetTheme} €</p>
                    `;
                    themeSection.appendChild(themeHeader);

            const table = document.createElement('table');
            table.className = 'propositions-table';
            table.innerHTML = `
                <thead>
                    <tr>
                        <th>Titre</th>
                        <th>Coût</th>
                        <th>Votes totaux</th>
                        <th>Votes satisfaits</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            `;

            const tbody = table.querySelector('tbody');
            if (Array.isArray(themeData.Propositions)) {
                themeData.Propositions.forEach(prop => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${prop.TitreProp || ''}</td>
                        <td>${prop.FraisProp || '0'} €</td>
                        <td>${prop.nbVotesTotal || '0'}</td>
                        <td>${prop.nbVotesSatisfaits || '0'}</td>
                        <td>
                            <button class="btn-select" data-id="${prop.IdProp}">
                                Sélectionner
                            </button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            }

            themeSection.appendChild(table);
            container.appendChild(themeSection);

                    container.appendChild(themeSection);
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            container.innerHTML = `
                <div class="error-message">
                    <p>Une erreur s'est produite: ${error.message}</p>
                    <p>Veuillez réessayer plus tard.</p>
                </div>
            `;
        });
}


function selectProposal(propId) {
    const idGroupe = new URLSearchParams(window.location.search).get('idGroupe') || 1;
    
    fetch('routeur.php?controleur=decideur&action=selectionner', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            idProposition: propId,
            idGroupe: idGroupe
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Proposition sélectionnée avec succès');
            loadPropositions(idGroupe, 'normal', 'json');
        } else {
            throw new Error(data.error || 'Une erreur est survenue');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Erreur lors de la sélection: ' + error.message);
    });
}
    </script>
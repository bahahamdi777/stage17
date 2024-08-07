<?php
// recevoir le noUtilisateur sent by login1.php

if (!isset($_GET['noUtilisateur'])) {
    echo "Utilisateur non authentifié.";
    exit();
}

$noUtilisateur = intval($_GET['noUtilisateur']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <title>Client Page</title>
    <style>
        body { 
            background-color: rgb(91, 139, 139);
        }
        .container {
            padding-left: 0;
            padding-right: 0;
        }
        .table-container {
            padding-left: 15px;
            padding-right: 15px;
        }
        table {
            width: 100%; /* S'assurer que le tableau prend toute la largeur disponible */
        }
        th, td {
            padding: 0.2rem; /*  l'espace dans les cellules du tableau */
            font-size: 0.85rem; /* la taille de la police */
        }
    </style>
</head>
<body>

    <?php include "navbar2.php"; ?>
     
    <div class="container mt-5">
        <h1 class="text-center">Bienvenue, Client!</h1>
        <h3 class="text-center">Voici vos commandes:  </h3>
        <h4 class="text-center">Numéro d'utilisateur: <?= htmlspecialchars($noUtilisateur); ?></h4> <!-- Affichez le numéro d'utilisateur ici -->

        <div id="error" class="alert alert-danger d-none" role="alert"></div>

        <div class="table">
            <table class="table table-striped  mt-4">
                <thead class="thead-dark">
                    <tr>
                        <th>Commande</th>
                        <th>Status</th>
                        <th>Nom Complet</th>
                        <th>Gouvernerat</th>
                        <th>Delegation</th>
                        <th>Localité</th>
                        <th>Adresse</th>
                        <th>Telephone</th>

                         <th>Désignation</th>
                        <th>Nombre d'Articles</th>
                        <th>Prix</th>
                        <th>Commentaire</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody id="commande-table-body">
                    <!-- Remplissez cette section avec JavaScript -->
                </tbody>
            </table>
        </div>
    </div>

    <script>
        async function fetchData() {
            const noUtilisateur = <?= json_encode($noUtilisateur); ?>; 
            try {
                const response = await fetch(`clientHistorique.php?noUtilisateur=${noUtilisateur}`);
                const data = await response.json();

                if (data.error) {
                    document.getElementById('error').innerText = data.error;
                    document.getElementById('error').classList.remove('d-none');
                    return;
                }

                let commandeTableContent = '';
                data.commandes.forEach(commande => {
                    // Determine the badge class based on the status
                    let statusClass;
                    switch (commande.status) {
                        case 'Livrée':
                            statusClass = 'badge bg-success'; // Green for completed
                            break;
                        case 'Traitement de la Commande':
                            statusClass = 'badge bg-warning text-dark'; // Yellow for pending
                            break;
                        case 'Annulée':
                            statusClass = 'badge bg-danger'; // Red for cancelled
                            break;
                        default:
                            statusClass = 'badge bg-secondary'; // Grey for unknown status
                            break;
                    }

                    commandeTableContent += `
                        <tr>
                            <td>${commande.code}</td>
                            <td><span class="${statusClass}">${commande.status}</span></td>
                            <td>${commande.nom_complet}</td>
                            <td>${commande.gouvernerat}</td>
                            <td>${commande.delegation}</td>
                            <td>${commande.localite}</td>
                            <td>${commande.adresse_complementaire}</td>
                            <td>${commande.telephone}</td>

                            <td>${commande.designation}</td>
                            <td>${commande.nombre_article}</td>
                            <td>${commande.prix}</td>
                            <td>${commande.commentaire || ''}</td>
                            <td>
                                <div class="d-flex">
                                    <a href="editComUser.php?code=${commande.code}" class="btn btn-danger btn-sm me-1" style="padding: 0.2rem 0.4rem; font-size: 0.75rem;">Modifier</a>
                                    <button type="button" class="btn btn-success btn-sm" onclick="deleteCommande(${commande.code})" style="padding: 0.2rem 0.4rem; font-size: 0.75rem;">Effacer</button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                document.getElementById('commande-table-body').innerHTML = commandeTableContent;

            } catch (error) {
                document.getElementById('error').innerText = 'Erreur lors de la récupération des données de commande.';
                document.getElementById('error').classList.remove('d-none');
            }
        }

        async function deleteCommande(code) {
            try {
                const response = await fetch('deleteCom.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `code=${code}`,
                });
                const result = await response.json();

                if (result.success) {
                    fetchData();
                } else {
                    document.getElementById('error').innerText = result.error;
                    document.getElementById('error').classList.remove('d-none');
                }
            } catch (error) {
                document.getElementById('error').innerText = 'Erreur lors de la suppression de la commande.';
                document.getElementById('error').classList.remove('d-none');
            }
        }

        window.onload = fetchData;
    </script>
</body>
</html>

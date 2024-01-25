<?php

// Include necessary files, establish database connection, and check admin session
// Include necessary files, establish database connection, and check admin session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
$bdd = new PDO("mysql:host=localhost;dbname=pret;charset=utf8", "root");
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
// Retrieve user details from the database based on user_id
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $query = $bdd->prepare("SELECT * FROM contracteurs WHERE id= ?");
    $query->execute([$user_id]);
    $userData = $query->fetch(PDO::FETCH_ASSOC);

    // Fetch user loans (adjust the query accordingly)
    $loansQuery = $bdd->prepare("SELECT * FROM loans WHERE user_id = :user_id");
    $loansQuery->bindParam(':user_id', $user_id);
    $loansQuery->execute();
    $userLoans = $loansQuery->fetch(PDO::FETCH_ASSOC);
}
// Fonction pour calculer la mensualité
function calculateMens($montantPret, $tauxInteretAnnuel, $dureeAnnees)
{
    // Convertir le taux d'intérêt annuel en taux d'intérêt mensuel
    $tauxInteretMensuel = ($tauxInteretAnnuel / 100) / 12;

    // Convertir la durée du prêt en nombre total de mensualités
    $nombreMensualites = $dureeAnnees * 12;

    // Calculer la mensualité en utilisant la formule du remboursement d'un prêt à amortissement constant
    $mensualite = round($montantPret * ($tauxInteretMensuel * pow(1 + $tauxInteretMensuel, $nombreMensualites)) / (pow(1 + $tauxInteretMensuel, $nombreMensualites) - 1), 0);

    return $mensualite;
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <div class="container">
        <?php if (isset($userData)): ?>
            <h1 class="mt-4 mb-4">User Details</h1>
            <ul class="list-group">
                <li class="list-group-item"><strong>Nom:</strong>
                    <?= $userData['nom']; ?>
                </li>
                <li class="list-group-item"><strong>Prénom:</strong>
                    <?= $userData['prenom']; ?>
                </li>
                <li class="list-group-item"><strong>Téléphone:</strong>
                    <?= $userData['tel']; ?>
                </li>
                <li class="list-group-item"><strong>Email:</strong>
                    <?= $userData['email']; ?>
                </li>
                <!-- Add more details as needed -->
            </ul>
        <?php else: ?>
            <p class="mt-4">User not found</p>
        <?php endif; ?>

        <!-- Loan Summary Section -->
        <div class="loan-summary mt-4">
            <h2>Relevé de prêt</h2>
            <table class="table">
                <thead>
                    <tr>
                        <th scope="col">Mois</th>
                        <th scope="col">Montant Mensuel</th>
                        <th scope="col">Montant Restant</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Initialisation du montant restant
                    $montantRestant = $userLoans['total_amount'];

                    // Utiliser DateTime pour obtenir le mois et l'année initiaux
                    $dateInitiale = new DateTime("January");
                    $moisInitial = $dateInitiale->format('F');
                    $anneeInitiale = $dateInitiale->format('Y');
                    $duree = $userData['duree'];
                    if ($duree <= 2) {
                        $taux = 8;
                    } elseif ($duree <= 4) {
                        $taux = 10;
                    } elseif ($duree <= 5) {
                        $taux = 12;
                    }

                    // Boucle à travers chaque mois du prêt
                    for ($mois = 1; $mois <= $duree * 12; $mois++) {
                        // Calcul du montant mensuel
                        $mensualite = calculateMens($userData['mt_pret'], $taux, $userData['duree']);

                        // Utiliser DateTime pour obtenir le nom complet du mois et l'année
                        $date = new DateTime("$moisInitial +$mois months");
                        $nomMois = $date->format('F'); // Obtient le nom complet du mois
                        $annee = $date->format('Y'); // Obtient l'année
                    
                        if ($mensualite > $montantRestant) {
                            $mensualite = $montantRestant;
                        }
                        echo "<tr>
            <th scope='row'>$nomMois $annee</th>
            <td>$mensualite FCFA</td>
            <td>$montantRestant FCFA</td>
          </tr>";
                        // Vérifier si le montant restant devient négatif
                    
                        $montantRestant -= $mensualite;




                        // Afficher la ligne du tableau
                    
                    }
                    ?>


                </tbody>
            </table>

            <!-- Bouton d'impression du relevé de prêt -->
            <button class="btn btn-primary mt-3" onclick="window.print()">Imprimer le relevé de prêt</button>
            <a href="../index.php" class="btn btn-primary mt-3">Retour</a>
        </div>
    </div>
</body>

</html>
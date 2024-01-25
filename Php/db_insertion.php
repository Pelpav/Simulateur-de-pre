<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$bdd = new PDO("mysql:host=localhost;dbname=pret;charset=utf8", "root");
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

function calculMensualite($montantPret, $tauxInteretAnnuel, $dureeAnnees)
{
    // Convertir le taux d'intérêt annuel en taux d'intérêt mensuel
    $tauxInteretMensuel = ($tauxInteretAnnuel / 100) / 12;

    // Convertir la durée du prêt en nombre total de mensualités
    $nombreMensualites = $dureeAnnees * 12;

    // Calculer la mensualité en utilisant la formule du remboursement d'un prêt à amortissement constant
    $mensualite = $montantPret * ($tauxInteretMensuel * pow(1 + $tauxInteretMensuel, $nombreMensualites)) / (pow(1 + $tauxInteretMensuel, $nombreMensualites) - 1);

    return $mensualite;
}
extract($_POST);
$success = false;
$maxtime = 5;
$mintime = 1;
$message = "Une erreur s'est produite, veuillez réessayer.";

// Validation côté serveur
if (empty($nom) || empty($prenom) || empty($tel) || empty($email) || empty($profession) || empty($entreprise) || empty($salaire) || empty($mt_pret) || empty($duree)) {
    $message = "Veuillez remplir tous les champs du formulaire.";
} elseif (strlen($tel) !== 8 || !is_numeric($tel)) {
    $message = "Le numéro de téléphone doit être composé de 8 chiffres.";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $message = "Veuillez entrer une adresse email valide.";
} elseif (!ctype_alpha($nom) || !ctype_alpha($prenom)) {
    $message = "Veuillez entrer un nom et un prénom valides (seules les lettres sont autorisées).";
} elseif (!ctype_alpha($profession)) {
    $message = "Veuillez entrer une profession valide (seules les lettres sont autorisées).";
} elseif (!ctype_alpha($entreprise)) {
    $message = "Veuillez entrer un nom d'entreprise valide (seules les lettres sont autorisées).";
} elseif (!is_numeric($salaire) || $salaire <= 0) {
    $message = "Veuillez entrer un salaire valide.";
} elseif (!is_numeric($mt_pret) || $mt_pret <= 0) {
    $message = "Veuillez entrer un montant de prêt valide.";
} elseif (!is_numeric($duree) || $duree <= 0 || $duree > 30) {
    $message = "Veuillez entrer une durée de prêt valide (entre 1 et 30 ans).";
} elseif (floatval($mt_pret) < floatval($salaire)) {
    $message = "Le montant du prêt ne peut pas être inférieur au salaire.";
} else {
    // Contrainte : La durée du prêt doit être entre 1 et 5 ans
    if ($duree <= 2) {
        $taux = 1.08;
    } elseif ($duree <= 4) {
        $taux = 1.10;
    } elseif ($duree <= 5) {
        $taux = 1.12;
    } else {
        $success = false;
        $message = "La durée du prêt doit être comprise entre $mintime et $maxtime ans.";
    }

    if (isset($taux)) {
        $mens = calculMensualite($mt_pret, $taux, $duree);

        if ($mens > $salaire / 3) {
            $success = false;
            $message = "Après calcul, la mensualité est supérieure au tiers de votre salaire, ce qui est prohibé. Merci de recommencer en ajustant la durée ou le montant du prêt.";
        } else {
            // Insérer le contracteur dans la table 'contracteurs'
            $insertContracteur = $bdd->prepare('INSERT INTO contracteurs (nom, prenom, tel, email, entreprise, mt_pret, duree) VALUES (?, ?, ?, ?, ?, ?, ?)');
            $insertContracteur->execute(array($nom, $prenom, $tel, $email, $entreprise, $mt_pret, $duree));

            // Récupérer l'ID généré après l'insertion
            $userId = $bdd->lastInsertId();

            // Calculer la date de paiement prévue (par exemple, chaque mois à partir de la date actuelle)
            $paymentDate = new DateTime();
            $paymentDate->add(new DateInterval('P1M')); // Ajoute 1 mois

            // Insérer le prêt dans la table 'loans'
            $insertLoan = $bdd->prepare('INSERT INTO loans (user_id, amount, duration, interest_rate, total_amount, payment_date) VALUES (?, ?, ?, ?, ?, ?)');
            $insertLoan->execute(array($userId, $mt_pret, $duree, $taux, $mt_pret * $taux, $paymentDate->format('Y-m-d')));

            $success = true;
            $message = "Votre contrat a bien été pris en compte, merci de vous acquitter de votre mensualité de $mens FCFA le plus tôt possible!";
        }
    }

}


// Assure que le script s'arrête ici
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Success</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>

<body>
    <p></p>
    <div class="container">

        <?php
        if ($success == true) {
            ?>
        <div class=" alert alert-success" role="alert">
            <h1 class="alert-heading">Success</h1>
            <p>
                <?php echo $message ?>
            </p>
            <a href="../index.html" class="btn btn-primary">Retour</a>
        </div>

        <?php
        } else { ?>
        <div class="alert alert-danger" role="alert">
            <h1 class="alert-heading">Echec</h1>
            <p>
                <?php echo $message ?>
            </p>
            <a href="../index.html" class="btn btn-primary">Retour</a>
        </div>
        <?php
        }
        ?>
    </div>
</body>

</html>
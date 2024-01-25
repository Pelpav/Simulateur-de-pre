<?php

// Include necessary files, establish database connection, and check admin session
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$bdd = new PDO("mysql:host=localhost;dbname=pret;charset=utf8", "root");
$bdd->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
$query = $bdd->query("SELECT * FROM contracteurs");
$users =
  $query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="fr">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard</title>
  <!-- Latest version of jQuery from CDN -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI/t1L9gG8o9OMLgT/Xh+4IbO7Sf4isiE9Rvnc4E=" crossorigin="anonymous"></script>

  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" />
</head>

<body>
  <div class="container">
    <h1>Admin Dashboard</h1>

    <table class="table">
      <thead>
        <tr>
          <th scope="col">ID</th>
          <th scope="col">Nom</th>
          <th scope="col">Prenom</th>
          <th scope="col">Téléphone</th>
          <th scope="col">Email</th>
          <th scope="col">Montant du Prêt</th>
          <th scope="col">Durée du Prêt</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $loan): ?>
          <tr class="user-row" data-user-id="<?= $loan['id']; ?>">
            <th scope="row" class="id">
              <?= $loan['id']; ?>
            </th>
            <td>
              <?= $loan['nom']; ?>
            </td>
            <td>
              <?= $loan['prenom']; ?>
            </td>
            <td>
              <?= $loan['tel']; ?>
            </td>
            <td>
              <?= $loan['email']; ?>
            </td>
            <td>
              <?= $loan['mt_pret']; ?>d
            </td>
            <td>
              <?= $loan['duree']; ?>
              ans
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <a align="center" class="btn btn-primary mt-3" href="deconnexion.php">Se déconnecter</a>
  <!-- Add this in the head section of your HTML file -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js"
    integrity="sha256-oP6HI/t1L9gG8o9OMLgT/Xh+4IbO7Sf4isiE9Rvnc4E=" crossorigin="anonymous"></script>
  <script type="text/javascript" src="JS/dashboard.js"></script>
</body>

</html>
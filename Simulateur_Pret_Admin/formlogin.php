<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
$defaultUsername = "admin";
$defaultPassword = "root";
$username = htmlspecialchars($_POST['username']);
$mdp = htmlspecialchars($_POST['mdp']);
$_SESSION['mdp'] = $mdp;

echo "Envoi";
if (!empty($_POST['username']) and !empty($_POST['mdp'])) {




    if ($defaultPassword = $mdp && $defaultUsername = $username) {
        $_SESSION['mdp'] = $mdp;
        header('Location: index.php');
    } else {
        echo "Votre mot de passe ou username est incorrect";
    }
} else {
    echo "Veuillez compléter tous les champs...";
}
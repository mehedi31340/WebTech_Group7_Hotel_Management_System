<?php
session_start();
if (empty($_SESSION['isLoggedIn'])) {
    header('Location: ../auth/View/login.php');
    exit;
}
$adminName = $_SESSION['loggedInUser'] ?? 'JUBAIR HOSSAIN';
$adminRole = $_SESSION['loggedInRole'] ?? 'Admin';
?>

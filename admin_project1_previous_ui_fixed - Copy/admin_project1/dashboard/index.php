<?php
session_start();

if (!empty($_SESSION["isLoggedIn"])) {
    header("Location: dashboard/dashboard.php");
} else {
    header("Location: auth/View/login.php");
}
exit;

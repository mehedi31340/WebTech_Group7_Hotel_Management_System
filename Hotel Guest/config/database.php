<?php
session_start();

$dbHost = "localhost";
$dbUser = "root";
$dbPass = "";
$dbName = "hotelguest";

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);

if ($conn->connect_error) {
    die("Database connection failed. Please import db.sql and check config/database.php.");
}

$conn->set_charset("utf8mb4");

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}

function require_login() {
    if (empty($_SESSION['guest_id'])) {
        header("Location: index.php?page=login");
        exit;
    }
}

function guest_id() {
    return (int)($_SESSION['guest_id'] ?? 0);
}

function money($amount) {
    return "৳ " . number_format((float)$amount, 2);
}

function redirect($url) {
    header("Location: $url");
    exit;
}
?>
<?php
session_start();

require_once __DIR__ . "/../../config/DatabaseConnection.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../View/registration.php");
    exit;
}

$name = trim($_POST["name"] ?? "");
$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";
$confirmPassword = $_POST["confirm_password"] ?? "";
$phone = trim($_POST["phone"] ?? "");
$role = $_POST["role"] ?? "guest";

$_SESSION["regName"] = $name;
$_SESSION["regEmail"] = $email;
$_SESSION["regPhone"] = $phone;
$_SESSION["regRole"] = $role;

$errors = [];

if ($name === "") {
    $errors["name"] = "Name is required";
} elseif (strlen($name) < 2) {
    $errors["name"] = "Name must be at least 2 characters";
} elseif (!preg_match("/^[a-zA-Z .'-]+$/", $name)) {
    $errors["name"] = "Name contains invalid characters";
}

if ($email === "") {
    $errors["email"] = "Email is required";
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors["email"] = "Enter a valid email address";
}

if ($password === "") {
    $errors["password"] = "Password is required";
} elseif (strlen($password) < 6) {
    $errors["password"] = "Password must be at least 6 characters";
}

if ($confirmPassword === "") {
    $errors["confirmPassword"] = "Please confirm your password";
} elseif ($password !== $confirmPassword) {
    $errors["confirmPassword"] = "Passwords do not match";
}

if ($phone !== "" && !preg_match("/^\+?[0-9]{7,15}$/", $phone)) {
    $errors["phone"] = "Enter a valid phone number";
}

$allowedRoles = ["guest", "receptionist", "housekeeping", "admin"];
if (!in_array($role, $allowedRoles, true)) {
    $errors["role"] = "Invalid role selected";
}

if (!empty($errors)) {
    $_SESSION["regErrors"] = $errors;
    header("Location: ../View/registration.php");
    exit;
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$stmt = $connection->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
if ($stmt === false) {
    $_SESSION["regErrors"] = ["general" => "Registration query failed: " . $connection->error];
    header("Location: ../View/registration.php");
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$exists = $result->num_rows > 0;
$stmt->close();

if ($exists) {
    $_SESSION["regErrors"] = ["email" => "An account with this email already exists"];
    header("Location: ../View/registration.php");
    exit;
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$stmt = $connection->prepare("INSERT INTO users (name, email, password_hash, phone, role) VALUES (?, ?, ?, ?, ?)");

if ($stmt === false) {
    $_SESSION["regErrors"] = ["general" => "Registration insert failed: " . $connection->error];
    header("Location: ../View/registration.php");
    exit;
}

$stmt->bind_param("sssss", $name, $email, $passwordHash, $phone, $role);

if ($stmt->execute()) {
    $stmt->close();
    $connection->close();
    unset($_SESSION["regName"], $_SESSION["regEmail"], $_SESSION["regPhone"], $_SESSION["regRole"], $_SESSION["regErrors"]);
    $_SESSION["registrationSuccess"] = "Registration successful. Please login.";
    header("Location: ../View/login.php");
    exit;
}

$_SESSION["regErrors"] = ["general" => "Registration failed: " . $stmt->error];
$stmt->close();
$connection->close();
header("Location: ../View/registration.php");
exit;

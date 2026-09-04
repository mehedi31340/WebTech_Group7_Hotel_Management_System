<?php
session_start();

require_once __DIR__ . "/../../config/DatabaseConnection.php";

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: ../View/login.php");
    exit;
}

$email = trim($_POST["email"] ?? "");
$password = $_POST["password"] ?? "";

$_SESSION["loginEmail"] = $email;
$hasError = false;

if ($email === "") {
    $_SESSION["loginEmailError"] = "Email is required";
    $hasError = true;
} elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["loginEmailError"] = "Enter a valid email address";
    $hasError = true;
} else {
    unset($_SESSION["loginEmailError"]);
}

if ($password === "") {
    $_SESSION["loginPasswordError"] = "Password is required";
    $hasError = true;
} elseif (strlen($password) < 6) {
    $_SESSION["loginPasswordError"] = "Password must be at least 6 characters";
    $hasError = true;
} else {
    unset($_SESSION["loginPasswordError"]);
}

if ($hasError) {
    header("Location: ../View/login.php");
    exit;
}

$db = new DatabaseConnection();
$connection = $db->openConnection();

$stmt = $connection->prepare("SELECT id, name, email, password_hash, role, is_active FROM users WHERE email = ? LIMIT 1");

if ($stmt === false) {
    $_SESSION["loginError"] = "Login query failed: " . $connection->error;
    header("Location: ../View/login.php");
    exit;
}

$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user || !password_verify($password, $user["password_hash"])) {
    $_SESSION["loginError"] = "Invalid email or password";
    header("Location: ../View/login.php");
    exit;
}

if (!(bool)$user["is_active"]) {
    $_SESSION["loginError"] = "This account is inactive";
    header("Location: ../View/login.php");
    exit;
}

session_regenerate_id(true);
$_SESSION["loggedInUser"] = $user["name"];
$_SESSION["loggedInUserId"] = $user["id"];
$_SESSION["loggedInRole"] = $user["role"];
$_SESSION["isLoggedIn"] = true;

unset($_SESSION["loginEmail"], $_SESSION["loginEmailError"], $_SESSION["loginPasswordError"], $_SESSION["loginError"]);

header("Location: ../../admin/dashboard.php");
exit;

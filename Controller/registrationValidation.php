<?php
require_once "../config/database.php";

$name = trim($_POST["name"] ?? "");
$username = trim($_POST["username"] ?? "");
$email = trim($_POST["email"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$nid = trim($_POST["nid"] ?? "");
$nationality = trim($_POST["nationality"] ?? "");
$date_of_birth = trim($_POST["date_of_birth"] ?? "");
$password = $_POST["password"] ?? "";

if ($name === "" || $email === "" || $phone === "" || $nid === "" || $nationality === "" || $date_of_birth === "" || $username === "" || $password === "") {
    $_SESSION["registerError"] = "All fields are required.";
    header("Location: ../View/registration.php"); exit();
}


if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $_SESSION["registerError"] = "Please enter a valid email address.";
    header("Location: ../View/registration.php"); exit();
}

$date = DateTime::createFromFormat("Y-m-d", $date_of_birth);
if (!$date || $date->format("Y-m-d") !== $date_of_birth || $date_of_birth > date("Y-m-d")) {
    $_SESSION["registerError"] = "Please enter a valid date of birth.";
    header("Location: ../View/registration.php"); exit();
}

$check = mysqli_prepare($conn, "SELECT id FROM users WHERE username=? OR email=? OR id_number=? LIMIT 1");
mysqli_stmt_bind_param($check, "sss", $username, $email, $nid);
mysqli_stmt_execute($check);
if (mysqli_num_rows(mysqli_stmt_get_result($check)) > 0) {
    $_SESSION["registerError"] = "Username, email, or NID already exists. Please use different information.";
    header("Location: ../View/registration.php"); exit();
}

$hash = password_hash($password, PASSWORD_DEFAULT);
$stmt = mysqli_prepare($conn, "INSERT INTO users (name, username, password, role, email, phone, id_number, nationality, date_of_birth) VALUES (?, ?, ?, 'receptionist', ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, "ssssssss", $name, $username, $hash, $email, $phone, $nid, $nationality, $date_of_birth);
mysqli_stmt_execute($stmt);

$_SESSION["registerSuccess"] = "Registration successful. Please log in.";
header("Location: ../View/login.php"); exit();
?>

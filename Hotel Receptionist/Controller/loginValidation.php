<?php
require_once "../config/database.php";

$username = trim($_POST["username"] ?? "");
$password = $_POST["password"] ?? "";

if (!$username || !$password) {
    $_SESSION["loginError"] = "Username and password are required";
    header("Location: ../View/login.php");
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT * FROM users WHERE username=? AND is_active=1 LIMIT 1");
mysqli_stmt_bind_param($stmt, "s", $username);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result && mysqli_num_rows($result) == 1) {
    $user = mysqli_fetch_assoc($result);

    if (password_verify($password, $user["password"])) {
        $_SESSION["user_id"] = $user["id"];
        $_SESSION["username"] = $user["username"];
        $_SESSION["name"] = $user["name"];
        $_SESSION["role"] = $user["role"];
        $_SESSION["isLoggedIn"] = true;

        if ($user["role"] !== "receptionist") {
            session_destroy();
            session_start();
            $_SESSION["loginError"] = "Only receptionist account can use this system";
            header("Location: ../View/login.php");
            exit();
        }

        header("Location: ../View/dashboard.php");
        exit();
    }
}

$_SESSION["loginError"] = "Username or password is incorrect";
header("Location: ../View/login.php");
exit();
?>

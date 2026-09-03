<?php
session_start();

if (isset($_SESSION["isLoggedIn"])) {
    header("Location: dashboard.php");
    exit();
}

$error = $_SESSION["loginError"] ?? "";
$success = $_SESSION["registerSuccess"] ?? "";

unset($_SESSION["loginError"], $_SESSION["registerSuccess"]);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Hotel Receptionist Login</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="login-box">

    <h2>Hotel Receptionist Login</h2>

    <?php if ($success != "") { ?>

        <p class="success">
            <?php echo htmlspecialchars($success); ?>
        </p>

    <?php } ?>

    <?php if ($error != "") { ?>

        <p class="error">
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php } ?>

    <form action="../Controller/loginValidation.php" method="post">

        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Login</button>

    </form>

    <p class="link">
        Don't have an account? <a href="registration.php">Register</a>
    </p>

</div>

</body>

</html>

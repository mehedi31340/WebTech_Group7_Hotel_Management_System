<?php
session_start();

$error = $_SESSION["registerError"] ?? "";
unset($_SESSION["registerError"]);
?>

<!DOCTYPE html>
<html>

<head>

    <title>Receptionist Registration</title>

    <link rel="stylesheet" href="style.css">

</head>

<body>

<div class="login-box">

    <h2>Receptionist Registration</h2>

    <?php if ($error != "") { ?>

        <p class="error">
            <?php echo htmlspecialchars($error); ?>
        </p>

    <?php } ?>

    <form action="../Controller/registrationValidation.php" method="post">

        <label for="name">Full Name</label>
        <input type="text" id="name" name="name" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" required>

        <label for="phone">Phone</label>
        <input type="tel" id="phone" name="phone" required>

        <label for="nid">NID Number</label>
        <input type="text" id="nid" name="nid" required>

        <label for="nationality">Nationality</label>
        <input type="text" id="nationality" name="nationality" required>

        <label for="date_of_birth">Date of Birth</label>
        <input type="date" id="date_of_birth" name="date_of_birth" required>

        <label for="username">Username</label>
        <input type="text" id="username" name="username" required>

        <label for="password">Password</label>
        <input type="password" id="password" name="password" required>

        <button type="submit">Register</button>

    </form>

    <p class="link">
        Already have an account? <a href="login.php">Login</a>
    </p>

</div>

</body>

</html>

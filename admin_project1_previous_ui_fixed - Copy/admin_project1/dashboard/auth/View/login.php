<?php
session_start();

if (!empty($_SESSION["isLoggedIn"])) {
    header("Location: ../../dashboard/dashboard.php");
    exit;
}

$emailError = $_SESSION["loginEmailError"] ?? "";
$passwordError = $_SESSION["loginPasswordError"] ?? "";
$loginError = $_SESSION["loginError"] ?? "";
$success = $_SESSION["registrationSuccess"] ?? "";
$emailValue = $_SESSION["loginEmail"] ?? "";

unset($_SESSION["loginEmailError"], $_SESSION["loginPasswordError"], $_SESSION["loginError"], $_SESSION["registrationSuccess"], $_SESSION["loginEmail"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZELO - Login</title>
    <link rel="stylesheet" href="../assets/auth.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card">
        <div class="brand">ZELO<span>ADMIN CONSOLE</span></div>
        <h1>Welcome back</h1>
        <p class="subtitle">Login to manage your hotel dashboard.</p>

        <?php if ($success): ?>
            <div class="message success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>

        <?php if ($loginError): ?>
            <div class="message error"><?php echo htmlspecialchars($loginError); ?></div>
        <?php endif; ?>

        <form action="../Controller/loginValidation.php" method="post" novalidate>
            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($emailValue); ?>" placeholder="admin@example.com" required>
                <?php if ($emailError): ?><p class="field-error"><?php echo htmlspecialchars($emailError); ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input id="password" type="password" name="password" placeholder="Enter your password" required>
                <?php if ($passwordError): ?><p class="field-error"><?php echo htmlspecialchars($passwordError); ?></p><?php endif; ?>
            </div>

            <button class="submit-btn" type="submit">Login</button>
        </form>

        <p class="switch-text">Don't have an account? <a href="registration.php">Create account</a></p>
    </div>
</div>
</body>
</html>

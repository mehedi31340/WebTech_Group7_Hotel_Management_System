<?php
session_start();

if (!empty($_SESSION["isLoggedIn"])) {
    header("Location: ../../dashboard/dashboard.php");
    exit;
}

$errors = $_SESSION["regErrors"] ?? [];
$nameValue = $_SESSION["regName"] ?? "";
$emailValue = $_SESSION["regEmail"] ?? "";
$phoneValue = $_SESSION["regPhone"] ?? "";
$roleValue = $_SESSION["regRole"] ?? "guest";

unset($_SESSION["regErrors"], $_SESSION["regName"], $_SESSION["regEmail"], $_SESSION["regPhone"], $_SESSION["regRole"]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZELO - Registration</title>
    <link rel="stylesheet" href="../assets/auth.css">
</head>
<body>
<div class="auth-page">
    <div class="auth-card registration-card">
        <div class="brand">ZELO<span>ADMIN CONSOLE</span></div>
        <h1>Create account</h1>
        <p class="subtitle">Register a new hotel management account.</p>

        <?php if (!empty($errors["general"])): ?>
            <div class="message error"><?php echo htmlspecialchars($errors["general"]); ?></div>
        <?php endif; ?>

        <form action="../Controller/regValidation.php" method="post" onsubmit="return validateRegistration()" novalidate>
            <div class="form-group">
                <label for="name">Full name</label>
                <input id="name" type="text" name="name" value="<?php echo htmlspecialchars($nameValue); ?>" required>
                <?php if (!empty($errors["name"])): ?><p class="field-error"><?php echo htmlspecialchars($errors["name"]); ?></p><?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="<?php echo htmlspecialchars($emailValue); ?>" required>
                <?php if (!empty($errors["email"])): ?><p class="field-error"><?php echo htmlspecialchars($errors["email"]); ?></p><?php endif; ?>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                    <?php if (!empty($errors["password"])): ?><p class="field-error"><?php echo htmlspecialchars($errors["password"]); ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm password</label>
                    <input id="confirm_password" type="password" name="confirm_password" required>
                    <?php if (!empty($errors["confirmPassword"])): ?><p class="field-error"><?php echo htmlspecialchars($errors["confirmPassword"]); ?></p><?php endif; ?>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input id="phone" type="tel" name="phone" value="<?php echo htmlspecialchars($phoneValue); ?>" placeholder="+8801XXXXXXXXX">
                    <?php if (!empty($errors["phone"])): ?><p class="field-error"><?php echo htmlspecialchars($errors["phone"]); ?></p><?php endif; ?>
                </div>
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <?php foreach (["guest" => "Guest", "receptionist" => "Receptionist", "housekeeping" => "Housekeeping", "admin" => "Admin"] as $value => $label): ?>
                            <option value="<?php echo $value; ?>" <?php echo $roleValue === $value ? "selected" : ""; ?>><?php echo $label; ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (!empty($errors["role"])): ?><p class="field-error"><?php echo htmlspecialchars($errors["role"]); ?></p><?php endif; ?>
                </div>
            </div>

            <button class="submit-btn" type="submit">Register</button>
        </form>

        <p class="switch-text">Already have an account? <a href="login.php">Login</a></p>
    </div>
</div>
<script src="../assets/auth.js"></script>
</body>
</html>

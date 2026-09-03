<?php 
 
$errors = $errors ?? []; 
 
$email = $email ?? ''; 
 
$authMessage = $_SESSION['auth_message'] ?? null; 
 
if ($authMessage !== null) { 
    unset($_SESSION['auth_message']); 
} 
 
?> 
 
<!DOCTYPE html> 
 
<html lang="en"> 
 
<head> 
 
<meta charset="UTF-8"> 
 
<meta name="viewport" content="width=device-width, initial-scale=1.0"> 
 
<link rel="stylesheet" href="css/auth.css"> 
 
<title>Housekeeping Supervisor Login</title> 

<style>
    .home-button {
        position: fixed;
        top: 20px;
        right: 25px;
        padding: 10px 18px;
        background-color: #2563eb;
        color: white;
        text-decoration: none;
        border-radius: 6px;
        font-size: 14px;
        z-index: 1000;
    }

    .home-button:hover {
        background-color: #1d4ed8;
    }
</style>
 
</head> 
 
<body> 

<a href="/hotel-portal/" class="home-button">Home</a>
 
 
<div class="auth-container"> 
 
    <h1>Housekeeping Supervisor Login</h1> 
 
    <p class="auth-subtitle"> 
        Sign in to access the housekeeping management system. 
    </p> 
 
 
    <?php if (isset($errors['general'])): ?> 
 
        <p class="general-error"> 
            <?= htmlspecialchars($errors['general']) ?> 
        </p> 
 
    <?php endif; ?> 
 
 
    <?php if ($authMessage !== null): ?> 
 
        <div class="auth-message"> 
            <?= htmlspecialchars($authMessage) ?> 
        </div> 
 
    <?php endif; ?> 
 
 
    <form method="POST" action="index.php?page=login"> 
 
        <div class="form-group"> 
 
            <label for="email">Email</label> 
 
            <input 
                type="email" 
                id="email" 
                name="email" 
                value="<?= htmlspecialchars($email) ?>" 
                required 
            > 
 
            <?php if (isset($errors['email'])): ?> 
 
                <span class="field-error"> 
                    <?= htmlspecialchars($errors['email']) ?> 
                </span> 
 
            <?php endif; ?> 
 
        </div> 
 
 
        <div class="form-group"> 
 
            <label for="password">Password</label> 
 
            <input 
                type="password" 
                id="password" 
                name="password" 
                autocomplete="current-password" 
                required 
            > 
 
            <?php if (isset($errors['password'])): ?> 
 
                <span class="field-error"> 
                    <?= htmlspecialchars($errors['password']) ?> 
                </span> 
 
            <?php endif; ?> 
 
        </div> 
 
 
        <button type="submit" class="auth-button"> 
            Login 
        </button> 
 
    </form> 
 
 
    <p class="auth-links"> 
        Don't have an account? 
        <a href="index.php?page=register">Register</a> 
    </p> 
 
 
    <div class="logout-section"> 
 
        <form method="POST" action="index.php?page=logout"> 
 
            <button 
                type="submit" 
                class="logout-button" 
                onclick="return confirm('Are you sure you want to log out?')" 
            > 
                Logout 
            </button> 
 
        </form> 
 
    </div> 
 
</div> 
 
 
</body> 
 
</html>
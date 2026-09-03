<?php

$errors = $errors ?? [];

$name = $name ?? '';
$email = $email ?? '';
$phone = $phone ?? '';
$nationality = $nationality ?? '';
$idNumber = $idNumber ?? '';

?>

<!DOCTYPE html>

<html lang="en">

<head>


<meta charset="UTF-8">

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<link rel="stylesheet" href="css/auth.css">

<title>Housekeeping Supervisor Registration</title>


</head>

<body>


<div class="form-grid">

    <h1>Housekeeping Supervisor Registration</h1>

   <!-- <p class="auth-subtitle">
        Create your Housekeeping Supervisor account.
    </p>-->


    <?php if (isset($errors['general'])): ?>

        <p class="general-error">
            <?= htmlspecialchars($errors['general']) ?>
        </p>

    <?php endif; ?>


    <form method="POST" action="index.php?page=register" autocomplete="off">

        <!-- Name -->

        <div class="form-group">

            <label for="name">Full Name</label>

            <input
                type="text"
                id="name"
                name="name"
                value="<?= htmlspecialchars($name) ?>"
            >

            <?php if (isset($errors['name'])): ?>

                <span class="field-error">
                    <?= htmlspecialchars($errors['name']) ?>
                </span>

            <?php endif; ?>

        </div>


        <!-- Email -->

        <div class="form-group">

            <label for="email">Email</label>

            <input
                type="email"
                id="email"
                name="email"
                value="<?= htmlspecialchars($email) ?>"
            >

            <?php if (isset($errors['email'])): ?>

                <span class="field-error">
                    <?= htmlspecialchars($errors['email']) ?>
                </span>

            <?php endif; ?>

        </div>


        <!-- Password -->

        <div class="form-group">

            <label for="password">Password</label>

            <input
                type="password"
                id="password"
                name="password"
            >

            <?php if (isset($errors['password'])): ?>

                <span class="field-error">
                    <?= htmlspecialchars($errors['password']) ?>
                </span>

            <?php endif; ?>

        </div>


        <!-- Phone -->

        <div class="form-group">

            <label for="phone">Phone</label>

            <input
                type="text"
                id="phone"
                name="phone"
                value="<?= htmlspecialchars($phone) ?>"
            >

            <?php if (isset($errors['phone'])): ?>

                <span class="field-error">
                    <?= htmlspecialchars($errors['phone']) ?>
                </span>

            <?php endif; ?>

        </div>


        <!-- Nationality -->

        <div class="form-group">

            <label for="nationality">Nationality</label>

            <input
                type="text"
                id="nationality"
                name="nationality"
                value="<?= htmlspecialchars($nationality) ?>"
            >

            <?php if (isset($errors['nationality'])): ?>

                <span class="field-error">
                    <?= htmlspecialchars($errors['nationality']) ?>
                </span>

            <?php endif; ?>

        </div>


        <!-- ID Number -->

        <div class="form-group">

            <label for="id_number">ID Number</label>

            <input
                type="text"
                id="id_number"
                name="id_number"
                value="<?= htmlspecialchars($idNumber) ?>"
            >

            <?php if (isset($errors['id_number'])): ?>

                <span class="field-error">
                    <?= htmlspecialchars($errors['id_number']) ?>
                </span>

            <?php endif; ?>

        </div>


        <button type="submit" class="auth-button">
            Register
        </button>

    </form>


    <p class="auth-links">
        Already have an account?
        <a href="index.php?page=login">Login</a>
    </p>


</div>


<script>

    window.addEventListener('pageshow', function () {

        const form = document.querySelector('form');

        if (form) {
            form.reset();
        }

    });

</script>


</body>

</html>

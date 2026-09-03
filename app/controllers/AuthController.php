<?php

require_once __DIR__ . '/../models/User.php';

class AuthController
{
    private User $userModel;

    public function __construct()
    {
        $this->userModel = new User();
    }

   public function register()
{
    $errors = [];

    $name = '';
    $email = '';
    $phone = '';
    $nationality = '';
    $idNumber = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $phone = trim($_POST['phone'] ?? '');
        $nationality = trim($_POST['nationality'] ?? '');
        $idNumber = trim($_POST['id_number'] ?? '');


        // Name validation
        if ($name === '') {
            $errors['name'] = "Name is required.";
        }


        // Email validation
        if ($email === '') {

            $errors['email'] = "Email is required.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $errors['email'] = "Please enter a valid email address.";

        } else {

            $existingUser = $this->userModel->findByEmail($email);

            if ($existingUser) {
                $errors['email'] = "An account with this email already exists.";
            }
        }


        // Password validation
        if ($password === '') {

            $errors['password'] = "Password is required.";

        } elseif (strlen($password) < 6) {

            $errors['password'] = "Password must be at least 6 characters.";
        }


        // Phone validation
        if ($phone === '') {
            $errors['phone'] = "Phone number is required.";
        }


        // Nationality validation
        if ($nationality === '') {
            $errors['nationality'] = "Nationality is required.";
        }


        // ID number validation
        if ($idNumber === '') {
             $errors['id_number'] = "ID number is required.";

} else {

    $existingId = $this->userModel->findByIdNumber($idNumber);

    if ($existingId) {
        $errors['id_number'] =
            "An account with this ID number already exists.";
    }
        }


        //_____________ If there are no errors, register the user
        if (empty($errors)) {

            $success = $this->userModel->register(
                $name,
                $email,
                $password,
                $phone,
                $nationality,
                $idNumber
            );

            if ($success) {

                header("Location: index.php?page=login");
                exit;

            } else {

                $errors['general'] =
                    "Registration failed. Please try again.";
            }
        }
    }

    require_once __DIR__ . '/../views/auth/register.php';
}

public function login()
{
    $errors = [];

    $email = '';

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';


        // Email validation
        if ($email === '') {

            $errors['email'] = "Email is required.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $errors['email'] = "Please enter a valid email address.";
        }


        // Password validation
        if ($password === '') {

            $errors['password'] = "Password is required.";
        }


        // Continue only if basic validation passed
        if (empty($errors)) {

            $user = $this->userModel->findByEmail($email);


            if (!$user || !password_verify($password, $user['password_hash'])) {

                $errors['general'] = "Invalid email or password.";

            } elseif ((int) $user['is_active'] !== 1) {

                $errors['general'] = "Your account is inactive.";

            } elseif ($user['role'] !== 'housekeeping') {

                $errors['general'] =
                    "You are not authorized to access housekeeping.";
            }


            // Login successful
            if (empty($errors)) {

                session_regenerate_id(true);

                $_SESSION['user_id'] = $user['id'];
                $_SESSION['name'] = $user['name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];

                header("Location: index.php?page=dashboard");
                exit;
            }
        }
    }

    require_once __DIR__ . '/../views/auth/login.php';
}

public function logout(): void
{
$_SESSION = [];


session_destroy();

header("Location: index.php?page=login");
exit;


}



}
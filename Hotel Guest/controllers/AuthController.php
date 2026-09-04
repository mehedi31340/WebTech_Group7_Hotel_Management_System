<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/User.php";

class AuthController {
    private User $users;
    public function __construct(mysqli $conn) { $this->users = new User($conn); }

    public function login(string $email, string $password): bool {
        $user = $this->users->findByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            $_SESSION['guest_id'] = $user['id'];
            $_SESSION['guest_name'] = $user['full_name'];
            return true;
        }
        return false;
    }
}
?>
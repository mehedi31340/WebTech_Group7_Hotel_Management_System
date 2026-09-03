<?php

require_once __DIR__ . '/../../config/database.php';

class User
{
    private mysqli $conn;

    public function __construct()
    {
        global $conn;

        $this->conn = $conn;
    }

    public function register(
        string $name,
        string $email,
        string $password,
        string $phone,
        string $nationality,
        string $idNumber
    ): bool {

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $sql = "
            INSERT INTO users
            (
                name,
                email,
                password_hash,
                phone,
                nationality,
                id_number,
                role
            )
            VALUES (?, ?, ?, ?, ?, ?, 'housekeeping')
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die(
                "Failed to prepare user registration query: "
                . $this->conn->error
            );
        }

        $stmt->bind_param(
            "ssssss",
            $name,
            $email,
            $passwordHash,
            $phone,
            $nationality,
            $idNumber
        );

        return $stmt->execute();
    }

    public function findByEmail(string $email): ?array
    {
        $sql = "
            SELECT
                id,
                name,
                email,
                password_hash,
                role,
                is_active
            FROM users
            WHERE email = ?
            LIMIT 1
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die(
                "Failed to prepare user lookup query: "
                . $this->conn->error
            );
        }

        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();

        $user = $result->fetch_assoc();

        return $user ?: null;
    }

public function findByIdNumber(string $idNumber): ?array
{
    $sql = "
        SELECT id
        FROM users
        WHERE id_number = ?
        LIMIT 1
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("Failed to prepare ID number lookup: " . $this->conn->error);
    }

    $stmt->bind_param("s", $idNumber);

    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_assoc() ?: null;
}

}
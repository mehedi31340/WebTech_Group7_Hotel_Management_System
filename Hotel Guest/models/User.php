<?php
class User {
    private mysqli $conn;
    public function __construct(mysqli $conn) { $this->conn = $conn; }

    public function findByEmail(string $email): ?array {
        $stmt = $this->conn->prepare("SELECT * FROM guests WHERE email=? LIMIT 1");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function create(array $data): int|false {
        $hash = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $this->conn->prepare(
            "INSERT INTO guests(full_name,email,phone,nationality,id_number,password_hash)
             VALUES(?,?,?,?,?,?)"
        );
        $stmt->bind_param("ssssss", $data['full_name'], $data['email'], $data['phone'],
            $data['nationality'], $data['id_number'], $hash);
        return $stmt->execute() ? $stmt->insert_id : false;
    }
}
?>
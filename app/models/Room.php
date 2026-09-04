<?php

require_once __DIR__ . '/../../config/database.php';

class Room
{
    private mysqli $conn;

    public function __construct()
    {
        global $conn;

        $this->conn = $conn;
    }

    public function getAllRooms()
    {
        $sql = "
            SELECT
                rooms.id,
                rooms.room_number,
                rooms.floor,
                rooms.status,
                rooms.notes,
                room_types.name AS room_type
            FROM rooms
            INNER JOIN room_types
                ON rooms.room_type_id = room_types.id
            ORDER BY rooms.floor, rooms.room_number
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die("Failed to prepare room query: " . $this->conn->error);
        }

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getRoomById(int $id)
{
    $sql = "
        SELECT
            rooms.id,
            rooms.room_number,
            rooms.floor,
            rooms.status,
            rooms.notes,
            room_types.name AS room_type
        FROM rooms
        INNER JOIN room_types
            ON rooms.room_type_id = room_types.id
        WHERE rooms.id = ?
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("Failed to prepare room lookup: " . $this->conn->error);
    }

    $stmt->bind_param("i", $id);

    $stmt->execute();

    $result = $stmt->get_result();

    return $result->fetch_assoc();
}

public function updateRoom(int $id, string $status,string $notes)
{
    $sql = "
        UPDATE rooms
        SET status = ?, notes = ?
        WHERE id = ?
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("Failed to prepare room update: " . $this->conn->error);
    }

    $stmt->bind_param("ssi", $status, $notes, $id);

    return $stmt->execute();
}

public function getRoomStatusCounts(): array
{
    $sql = "
        SELECT
            status,
            COUNT(*) AS total
        FROM rooms
        GROUP BY status
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("Failed to prepare room status count query: " . $this->conn->error);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $counts = [
    'available' => 0,
    'occupied' => 0,
    'dirty' => 0,
    'in_progress' => 0,
    'maintenance' => 0,
    'blocked' => 0

    ];

    while ($row = $result->fetch_assoc()) {
        $counts[$row['status']] = (int) $row['total'];
    }

    return $counts;
}

}
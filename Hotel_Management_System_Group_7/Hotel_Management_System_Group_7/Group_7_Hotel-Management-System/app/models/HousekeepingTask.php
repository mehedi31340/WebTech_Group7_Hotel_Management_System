<?php

require_once __DIR__ . '/../../config/database.php';

class HousekeepingTask
{
    private mysqli $conn;

    public function __construct()
    {
        global $conn;

        $this->conn = $conn;
    }

    public function getAllTasks(): array
    {
        $sql = "
            SELECT
                housekeeping_tasks.id,
                housekeeping_tasks.room_id,
                rooms.room_number,
                housekeeping_tasks.assigned_to,
                users.name AS assigned_to_name,
                housekeeping_tasks.task_type,
                housekeeping_tasks.priority,
                housekeeping_tasks.status,
                housekeeping_tasks.notes,
                housekeeping_tasks.scheduled_date,
                housekeeping_tasks.completed_at
            FROM housekeeping_tasks

            INNER JOIN rooms
                ON housekeeping_tasks.room_id = rooms.id

            INNER JOIN users
                ON housekeeping_tasks.assigned_to = users.id

            ORDER BY housekeeping_tasks.scheduled_date ASC,
                     housekeeping_tasks.id ASC
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die(
                "Failed to prepare housekeeping task query: "
                . $this->conn->error
            );
        }

        $stmt->execute();

        $result = $stmt->get_result();

        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function getTaskSummary(): array
{
    $sql = "
        SELECT
            COUNT(CASE WHEN status = 'pending' THEN 1 END) AS pending,
            COUNT(CASE WHEN status = 'in_progress' THEN 1 END) AS in_progress,
            COUNT(CASE WHEN status = 'done' THEN 1 END) AS completed,
            COUNT(CASE WHEN priority = 'urgent' THEN 1 END) AS urgent
        FROM housekeeping_tasks
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("Failed to prepare task summary query: " . $this->conn->error);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $summary = $result->fetch_assoc();

    return [
        'pending' => (int) $summary['pending'],
        'in_progress' => (int) $summary['in_progress'],
        'completed' => (int) $summary['completed'],
        'urgent' => (int) $summary['urgent']
    ];
}

public function getAllRooms(): array
{
    $sql = "
        SELECT id, room_number
        FROM rooms
        ORDER BY room_number
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("Failed to prepare rooms query: " . $this->conn->error);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $rooms = [];

    while ($row = $result->fetch_assoc()) {
        $rooms[] = $row;
    }

    return $rooms;
}

public function getHousekeepers(): array
{
    $sql = "
        SELECT id, name
        FROM users
        WHERE role = 'housekeeping'
          AND is_active = 1
        ORDER BY name
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die("Failed to prepare housekeepers query: " . $this->conn->error);
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $housekeepers = [];

    while ($row = $result->fetch_assoc()) {
        $housekeepers[] = $row;
    }

    return $housekeepers;
}

public function createTask(
    int $roomId,
    int $assignedTo,
    string $taskType,
    string $priority,
    string $scheduledDate
): bool {

    $sql = "
        INSERT INTO housekeeping_tasks
        (
            room_id,
            assigned_to,
            task_type,
            priority,
            scheduled_date
        )
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare create task query: "
            . $this->conn->error
        );
    }

    $stmt->bind_param(
        "iisss",
        $roomId,
        $assignedTo,
        $taskType,
        $priority,
        $scheduledDate
    );

    return $stmt->execute();
}

public function getTaskById(int $taskId): ?array
{
    $sql = "
        SELECT
            ht.id,
            ht.room_id,
            r.room_number,
            ht.assigned_to,
            u.name AS assigned_to_name,
            ht.task_type,
            ht.priority,
            ht.status,
            ht.scheduled_date,
            ht.completed_at
        FROM housekeeping_tasks ht
        INNER JOIN rooms r
            ON ht.room_id = r.id
        INNER JOIN users u
            ON ht.assigned_to = u.id
        WHERE ht.id = ?
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare task query: "
            . $this->conn->error
        );
    }

    $stmt->bind_param("i", $taskId);

    $stmt->execute();

    $result = $stmt->get_result();

    $task = $result->fetch_assoc();

    return $task ?: null;
}


public function updateTask(
    int $taskId,
    int $assignedTo,
    string $taskType,
    string $priority,
    string $status,
    string $scheduledDate
): bool {

    if ($status === 'done') {

        $sql = "
            UPDATE housekeeping_tasks
            SET
                assigned_to = ?,
                task_type = ?,
                priority = ?,
                status = ?,
                scheduled_date = ?,
                completed_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ";

    } else {

        $sql = "
            UPDATE housekeeping_tasks
            SET
                assigned_to = ?,
                task_type = ?,
                priority = ?,
                status = ?,
                scheduled_date = ?,
                completed_at = NULL
            WHERE id = ?
        ";
    }

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare update task query: "
            . $this->conn->error
        );
    }

    $stmt->bind_param(
        "issssi",
        $assignedTo,
        $taskType,
        $priority,
        $status,
        $scheduledDate,
        $taskId
    );

    return $stmt->execute();
}


public function deleteTask(int $taskId): bool
{
    $sql = "
        DELETE FROM housekeeping_tasks
        WHERE id = ?
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare delete task query: "
            . $this->conn->error
        );
    }

    $stmt->bind_param("i", $taskId);

    return $stmt->execute();
}

public function getPendingInspectionCount(): int
{
    $sql = "
        SELECT COUNT(*) AS total
        FROM housekeeping_tasks
        WHERE task_type = 'inspection'
          AND status = 'pending'
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare pending inspection query: "
            . $this->conn->error
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    return (int) $row['total'];
}

public function getCompletedTaskCount(): int
{
    $sql = "
        SELECT COUNT(*) AS total
        FROM housekeeping_tasks
        WHERE status = 'done'
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare completed task query: "
            . $this->conn->error
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    return (int) $row['total'];
}


}
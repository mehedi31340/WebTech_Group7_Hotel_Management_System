<?php

require_once __DIR__ . '/../../config/database.php';

class MaintenanceReport
{
    private mysqli $conn;

    public function __construct()
    {
        global $conn;

        $this->conn = $conn;
    }

    public function getAllReports(): array
    {
        $sql = "
            SELECT
                mr.id,
                mr.room_id,
                r.room_number,
                mr.reported_by,
                u.name AS reported_by_name,
                mr.description,
                mr.severity,
                mr.status,
                mr.reported_at,
                mr.resolved_at
            FROM maintenance_reports mr

            INNER JOIN rooms r
                ON mr.room_id = r.id

            INNER JOIN users u
                ON mr.reported_by = u.id

            ORDER BY mr.reported_at DESC
        ";

        $stmt = $this->conn->prepare($sql);

        if (!$stmt) {
            die(
                "Failed to prepare maintenance reports query: "
                . $this->conn->error
            );
        }

        $stmt->execute();

        $result = $stmt->get_result();

        $reports = [];

        while ($row = $result->fetch_assoc()) {
            $reports[] = $row;
        }

        return $reports;
    }

public function getMaintenanceSummary(): array
{
    $sql = "
        SELECT
            COUNT(CASE WHEN status = 'open' THEN 1 END) AS open_issues,
            COUNT(CASE WHEN status = 'in_progress' THEN 1 END) AS in_progress,
            COUNT(CASE WHEN status = 'resolved' THEN 1 END) AS resolved,
            COUNT(CASE WHEN severity = 'high' THEN 1 END) AS high_severity
        FROM maintenance_reports
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare maintenance summary query: "
            . $this->conn->error
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $summary = $result->fetch_assoc();

    return [
        'open_issues' => (int) $summary['open_issues'],
        'in_progress' => (int) $summary['in_progress'],
        'resolved' => (int) $summary['resolved'],
        'high_severity' => (int) $summary['high_severity']
    ];
}


public function createReport(
    int $roomId,
    int $reportedBy,
    string $description,
    string $severity,
    string $status
): bool {

    $sql = "
        INSERT INTO maintenance_reports
        (
            room_id,
            reported_by,
            description,
            severity,
            status
        )
        VALUES (?, ?, ?, ?, ?)
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare maintenance report query: "
            . $this->conn->error
        );
    }

    $stmt->bind_param(
        "iisss",
        $roomId,
        $reportedBy,
        $description,
        $severity,
        $status
    );

    return $stmt->execute();
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
        die(
            "Failed to prepare rooms query: "
            . $this->conn->error
        );
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
        die(
            "Failed to prepare housekeepers query: "
            . $this->conn->error
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $housekeepers = [];

    while ($row = $result->fetch_assoc()) {
        $housekeepers[] = $row;
    }

    return $housekeepers;
}

public function getReportById(int $reportId): ?array
{
    $sql = "
        SELECT
            mr.id,
            mr.room_id,
            r.room_number,
            mr.reported_by,
            u.name AS reported_by_name,
            mr.description,
            mr.severity,
            mr.status,
            mr.reported_at,
            mr.resolved_at
        FROM maintenance_reports mr

        INNER JOIN rooms r
            ON mr.room_id = r.id

        INNER JOIN users u
            ON mr.reported_by = u.id

        WHERE mr.id = ?
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare maintenance report query: "
            . $this->conn->error
        );
    }

    $stmt->bind_param("i", $reportId);

    $stmt->execute();

    $result = $stmt->get_result();

    $report = $result->fetch_assoc();

    return $report ?: null;
}

public function updateReport(
    int $reportId,
    string $description,
    string $severity,
    string $status
): bool {

    if ($status === 'resolved') {

        $sql = "
            UPDATE maintenance_reports
            SET
                description = ?,
                severity = ?,
                status = ?,
                resolved_at = CURRENT_TIMESTAMP
            WHERE id = ?
        ";

    } else {

        $sql = "
            UPDATE maintenance_reports
            SET
                description = ?,
                severity = ?,
                status = ?,
                resolved_at = NULL
            WHERE id = ?
        ";
    }


    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare maintenance update query: "
            . $this->conn->error
        );
    }


    $stmt->bind_param(
        "sssi",
        $description,
        $severity,
        $status,
        $reportId
    );


    return $stmt->execute();
}

public function deleteReport(int $reportId): bool
{
    $sql = "
        DELETE FROM maintenance_reports
        WHERE id = ?
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare delete maintenance report query: "
            . $this->conn->error
        );
    }

    $stmt->bind_param("i", $reportId);

    return $stmt->execute();
}

public function getTotalReportCount(): int
{
    $sql = "
        SELECT COUNT(*) AS total
        FROM maintenance_reports
    ";

    $stmt = $this->conn->prepare($sql);

    if (!$stmt) {
        die(
            "Failed to prepare total maintenance report query: "
            . $this->conn->error
        );
    }

    $stmt->execute();

    $result = $stmt->get_result();

    $row = $result->fetch_assoc();

    return (int) $row['total'];
}

}
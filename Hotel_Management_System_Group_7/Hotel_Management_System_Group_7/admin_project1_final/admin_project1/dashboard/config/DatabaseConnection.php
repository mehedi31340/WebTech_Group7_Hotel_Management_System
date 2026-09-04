<?php

class DatabaseConnection
{
    private string $host = 'localhost';
    private string $username = 'root';
    private string $password = '';
    private string $database = 'hoteldb';

    public function openConnection(): mysqli
    {
        $connection = new mysqli(
            $this->host,
            $this->username,
            $this->password,
            $this->database
        );

        if ($connection->connect_errno) {
            die('Database connection failed: ' . $connection->connect_error);
        }

        if (!$connection->set_charset('utf8mb4')) {
            die('Database charset configuration failed: ' . $connection->error);
        }

        return $connection;
    }

    public function getAllRooms(mysqli $connection): mysqli_result|false
    {
        $sql = "SELECT r.id, r.room_number, r.floor, r.status,
                       rt.name AS type_name
                FROM rooms r
                INNER JOIN room_type rt ON rt.id = r.room_type_id
                ORDER BY r.floor, r.room_number";

        return $connection->query($sql);
    }

    public function getAllRoomTypes(mysqli $connection): mysqli_result|false
    {
        return $connection->query(
            "SELECT id, name, description, price_per_night, max_capacity, thumbnail_path, amenities
             FROM room_type
             ORDER BY id"
        );
    }
public function getAllBookings(mysqli $connection): mysqli_result|false
{
    $sql = "SELECT b.id, 
                   u.name AS guest_name, 
                   r.room_number, 
                   rt.name AS room_type, 
                   b.checkin_date, 
                   b.checkout_date, 
                   b.source, 
                   b.status 
            FROM bookings b 
            INNER JOIN users u ON u.id = b.guest_id 
            LEFT JOIN rooms r ON r.id = b.room_id 
            LEFT JOIN room_type rt ON rt.id = r.room_type_id 
            ORDER BY b.checkin_date DESC, b.id DESC";

    return $connection->query($sql);
}

    public function getAllReviews(mysqli $connection): mysqli_result|false
    {
        $sql = "SELECT rv.id,
                       u.name AS guest_name,
                       rv.overall_rating,
                       rv.cleanliness_rating,
                       rv.service_rating,
                       rv.review_text,
                       rv.admin_reply,
                       rv.created_at,
                       r.room_number,
                       rt.name AS room_type
                FROM reviews rv
                INNER JOIN users u ON u.id = rv.guest_id
                INNER JOIN bookings b ON b.id = rv.booking_id
                LEFT JOIN rooms r ON r.id = b.room_id
                LEFT JOIN room_type rt ON rt.id = r.room_type_id
                ORDER BY rv.created_at DESC, rv.id DESC";

        return $connection->query($sql);
    }

public function getFinancialReport(mysqli $connection): mysqli_result|false
{
    $sql = "SELECT rt.name AS room_type,
                   COUNT(b.id) AS total_bookings,
                   COALESCE(
                       SUM(
                           GREATEST(
                               DATEDIFF(b.checkout_date, b.checkin_date),
                               0
                           ) * rt.price_per_night
                       ),
                       0
                   ) AS revenue
            FROM bookings b
            INNER JOIN rooms r ON r.id = b.room_id
            INNER JOIN room_type rt ON rt.id = r.room_type_id
            WHERE b.status IN ('confirmed', 'completed')
            GROUP BY rt.id, rt.name
            ORDER BY revenue DESC";

    return $connection->query($sql);
}
}

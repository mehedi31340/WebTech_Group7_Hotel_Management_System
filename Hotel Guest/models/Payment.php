<?php
class Payment {
    private mysqli $conn;
    public function __construct(mysqli $conn) { $this->conn = $conn; }

    public function invoices(int $guestId): mysqli_result {
        $stmt=$this->conn->prepare("SELECT i.*,b.booking_code,r.name FROM invoices i
          JOIN bookings b ON b.id=i.booking_id JOIN room_types r ON r.id=b.room_type_id
          WHERE b.guest_id=? ORDER BY i.id DESC");
        $stmt->bind_param("i",$guestId); $stmt->execute();
        return $stmt->get_result();
    }
}
?>
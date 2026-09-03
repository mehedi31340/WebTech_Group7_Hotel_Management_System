<?php
class Booking {
    private mysqli $conn;
    public function __construct(mysqli $conn) { $this->conn = $conn; }

    public function byGuest(int $guestId): mysqli_result {
        $stmt=$this->conn->prepare("SELECT b.*,r.name room_name FROM bookings b
          JOIN room_types r ON r.id=b.room_type_id WHERE b.guest_id=? ORDER BY b.check_in DESC");
        $stmt->bind_param("i",$guestId); $stmt->execute();
        return $stmt->get_result();
    }

    public function findForGuest(int $id, int $guestId): ?array {
        $stmt=$this->conn->prepare("SELECT b.*,r.name room_name FROM bookings b
          JOIN room_types r ON r.id=b.room_type_id WHERE b.id=? AND b.guest_id=?");
        $stmt->bind_param("ii",$id,$guestId); $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }
}
?>
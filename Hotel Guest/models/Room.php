<?php
class Room {
    private mysqli $conn;
    public function __construct(mysqli $conn) { $this->conn = $conn; }

    public function find(int $id): ?array {
        $stmt=$this->conn->prepare("SELECT * FROM room_types WHERE id=?");
        $stmt->bind_param("i",$id); $stmt->execute();
        return $stmt->get_result()->fetch_assoc() ?: null;
    }

    public function search(string $checkin, string $checkout, int $guests): mysqli_result {
        $stmt=$this->conn->prepare("SELECT rt.*,
          (SELECT COUNT(*) FROM rooms rm WHERE rm.room_type_id=rt.id AND rm.status='available') total_rooms,
          (SELECT COUNT(*) FROM bookings b WHERE b.room_type_id=rt.id
           AND b.status IN('confirmed','upcoming') AND b.check_in < ? AND b.check_out > ?) booked
          FROM room_types rt WHERE rt.capacity >= ? ORDER BY rt.price_per_night");
        $stmt->bind_param("ssi",$checkout,$checkin,$guests);
        $stmt->execute();
        return $stmt->get_result();
    }
}
?>
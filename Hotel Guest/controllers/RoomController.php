<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Room.php";

class RoomController {
    private Room $rooms;
    public function __construct(mysqli $conn) { $this->rooms = new Room($conn); }
    public function search(string $checkin, string $checkout, int $guests): mysqli_result {
        return $this->rooms->search($checkin, $checkout, $guests);
    }
}
?>
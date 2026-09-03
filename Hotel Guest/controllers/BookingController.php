<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Booking.php";

class BookingController {
    private Booking $bookings;
    public function __construct(mysqli $conn) { $this->bookings = new Booking($conn); }
    public function listForGuest(int $guestId): mysqli_result { return $this->bookings->byGuest($guestId); }
}
?>
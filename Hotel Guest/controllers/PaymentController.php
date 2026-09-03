<?php
require_once __DIR__ . "/../config/database.php";
require_once __DIR__ . "/../models/Payment.php";

class PaymentController {
    private Payment $payments;
    public function __construct(mysqli $conn) { $this->payments = new Payment($conn); }
    public function history(int $guestId): mysqli_result { return $this->payments->invoices($guestId); }
}
?>
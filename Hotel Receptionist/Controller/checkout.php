<?php
require_once "../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: ../View/login.php");
    exit();
}

$id = (int)($_GET["id"] ?? 0);

$stmt = mysqli_prepare(
    $conn,
    "SELECT b.room_id, bl.payment_status, bl.total_amount
     FROM bookings b
     LEFT JOIN billing bl ON bl.booking_id = b.id
     WHERE b.id = ? AND b.status = 'Checked In'"
);
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row) {
    $_SESSION["bookingError"] = "Booking #$id could not be checked out (not found or not Checked In).";
    header("Location: ../View/dashboard.php");
    exit();
}

if ($row["payment_status"] !== "paid") {
    $due = $row["total_amount"] !== null ? $row["total_amount"] : "an unpaid";
    $_SESSION["bookingError"] = "Booking #$id cannot be checked out until payment is completed. Outstanding balance: $due BDT.";
    header("Location: ../View/dashboard.php");
    exit();
}

$s = mysqli_prepare($conn, "UPDATE bookings SET status='Checked Out' WHERE id=?");
mysqli_stmt_bind_param($s, "i", $id);
mysqli_stmt_execute($s);

$s = mysqli_prepare($conn, "UPDATE rooms SET status='Dirty' WHERE id=?");
mysqli_stmt_bind_param($s, "i", $row["room_id"]);
mysqli_stmt_execute($s);

$_SESSION["bookingMessage"] = "Booking #$id checked out. Room marked Dirty for housekeeping.";

header("Location: ../View/dashboard.php");
exit();
?>

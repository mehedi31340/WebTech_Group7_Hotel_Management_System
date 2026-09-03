<?php
require_once "../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: ../View/login.php");
    exit();
}

$booking_id = (int)($_POST["booking_id"] ?? 0);
$type = trim($_POST["service_type"] ?? "");
$desc = trim($_POST["description"] ?? "");
$amountInput = trim($_POST["amount"] ?? "");
$allowed = ["extra_bed", "toiletries", "laundry", "room_service", "other"];

// Default price per service type (BDT). The receptionist can override
// this per request via the Amount field.
$defaultPrices = [
    "extra_bed" => 500,
    "toiletries" => 100,
    "laundry" => 300,
    "room_service" => 400,
    "other" => 0,
];

if ($booking_id <= 0 || !in_array($type, $allowed, true)) {
    $_SESSION["serviceError"] = "Please enter a valid checked-in Booking ID and service.";
    header("Location: ../View/dashboard.php");
    exit();
}

if ($amountInput !== "" && (!is_numeric($amountInput) || (float)$amountInput < 0)) {
    $_SESSION["serviceError"] = "Amount must be a non-negative number.";
    header("Location: ../View/dashboard.php");
    exit();
}

$price = $amountInput !== "" ? (float)$amountInput : $defaultPrices[$type];

try {
    $check = mysqli_prepare(
        $conn,
        "SELECT room_id FROM bookings WHERE id = ? AND status = 'Checked In' LIMIT 1"
    );
    mysqli_stmt_bind_param($check, "i", $booking_id);
    mysqli_stmt_execute($check);
    $bookingRow = mysqli_fetch_assoc(mysqli_stmt_get_result($check));

    if (!$bookingRow) {
        $_SESSION["serviceError"] = "No checked-in booking found with ID #$booking_id.";
        header("Location: ../View/dashboard.php");
        exit();
    }

    $room_id = (int)$bookingRow["room_id"];

    $stmt = mysqli_prepare(
        $conn,
        "INSERT INTO service_requests (booking_id, room_id, service_type, description, price, status)
         VALUES (?, ?, ?, ?, ?, 'pending')"
    );
    mysqli_stmt_bind_param($stmt, "iissd", $booking_id, $room_id, $type, $desc, $price);
    mysqli_stmt_execute($stmt);

    // The service charge is now part of what the guest owes.
    recalcBilling($conn, $booking_id);

    $_SESSION["serviceMessage"] = "Service request added successfully ($price BDT added to the bill).";
} catch (Throwable $e) {
    error_log("Hotel service request error: " . $e->getMessage());
    $_SESSION["serviceError"] = "Service request could not be added.";
}

header("Location: ../View/dashboard.php");
exit();
?>

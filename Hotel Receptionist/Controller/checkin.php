<?php
require_once "../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: ../View/login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    $_SESSION["bookingError"] = "Use the Check In form on the dashboard to select a payment method.";
    header("Location: ../View/dashboard.php");
    exit();
}

$id = (int)($_POST["booking_id"] ?? 0);
$paymentMethod = $_POST["payment_method"] ?? "";
$allowedMethods = ["Cash", "Card", "Mobile Banking"];

// Guests must pay at check-in, so a payment method is required here just
// like it is for the instant walk-in flow.
if (!in_array($paymentMethod, $allowedMethods, true)) {
    $_SESSION["bookingError"] = "Select a payment method to check in booking #$id.";
    header("Location: ../View/dashboard.php");
    exit();
}

try {
    mysqli_begin_transaction($conn);

    $stmt = mysqli_prepare($conn, "SELECT room_id FROM bookings WHERE id=? AND status='Booked' FOR UPDATE");
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

    if (!$row) {
        mysqli_rollback($conn);
        $_SESSION["bookingError"] = "Booking #$id could not be checked in (not found or not in Booked status).";
        header("Location: ../View/dashboard.php");
        exit();
    }

    $s = mysqli_prepare($conn, "UPDATE bookings SET status='Checked In' WHERE id=?");
    mysqli_stmt_bind_param($s, "i", $id);
    mysqli_stmt_execute($s);

    $s = mysqli_prepare($conn, "UPDATE rooms SET status='Occupied' WHERE id=?");
    mysqli_stmt_bind_param($s, "i", $row["room_id"]);
    mysqli_stmt_execute($s);

    $charged = markBillingPaid($conn, $id, $paymentMethod);

    mysqli_commit($conn);

    $_SESSION["bookingMessage"] = "Booking #$id checked in. Room is now Occupied. Payment of $charged BDT recorded ($paymentMethod).";
} catch (Throwable $e) {
    if ($conn->thread_id) {
        try { mysqli_rollback($conn); } catch (Throwable $ignored) {}
    }
    error_log("Hotel check-in error: " . $e->getMessage());
    $_SESSION["bookingError"] = "Booking #$id could not be checked in. Please try again.";
}

header("Location: ../View/dashboard.php");
exit();
?>

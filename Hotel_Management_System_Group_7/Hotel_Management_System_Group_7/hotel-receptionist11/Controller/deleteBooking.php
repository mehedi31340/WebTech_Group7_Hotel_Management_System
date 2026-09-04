<?php
require_once "../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: ../View/login.php");
    exit();
}

$id = (int)($_GET["id"] ?? 0);

$stmt = mysqli_prepare($conn, "SELECT room_id, guest_name FROM bookings WHERE id = ?");
mysqli_stmt_bind_param($stmt, "i", $id);
mysqli_stmt_execute($stmt);
$row = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));

if (!$row) {
    $_SESSION["bookingError"] = "Booking #$id not found.";
    header("Location: ../View/dashboard.php");
    exit();
}

try {
    mysqli_begin_transaction($conn);

    // On a fresh database.sql install, billing, service_requests, and
    // early_late_requests have ON DELETE CASCADE on booking_id, so
    // removing the booking cleans up its related records automatically.
    // But databases set up via upgrade_existing_database.sql create these
    // tables without that foreign key, so a plain DELETE FROM bookings
    // would fail there. Delete the dependent rows explicitly first: this
    // works regardless of which schema path was used, and is a harmless
    // no-op on databases where cascade already removed them.
    $delBilling = mysqli_prepare($conn, "DELETE FROM billing WHERE booking_id = ?");
    mysqli_stmt_bind_param($delBilling, "i", $id);
    mysqli_stmt_execute($delBilling);

    $delService = mysqli_prepare($conn, "DELETE FROM service_requests WHERE booking_id = ?");
    mysqli_stmt_bind_param($delService, "i", $id);
    mysqli_stmt_execute($delService);

    $delEarlyLate = mysqli_prepare($conn, "DELETE FROM early_late_requests WHERE booking_id = ?");
    mysqli_stmt_bind_param($delEarlyLate, "i", $id);
    mysqli_stmt_execute($delEarlyLate);

    $del = mysqli_prepare($conn, "DELETE FROM bookings WHERE id = ?");
    mysqli_stmt_bind_param($del, "i", $id);
    mysqli_stmt_execute($del);

    // The physical room this booking was using is free again.
    $roomUpdate = mysqli_prepare($conn, "UPDATE rooms SET status = 'Available' WHERE id = ?");
    mysqli_stmt_bind_param($roomUpdate, "i", $row["room_id"]);
    mysqli_stmt_execute($roomUpdate);

    mysqli_commit($conn);

    $_SESSION["bookingMessage"] = "Booking #$id (" . $row["guest_name"] . ") deleted. Room is now Available.";
} catch (Throwable $e) {
    mysqli_rollback($conn);
    error_log("Hotel delete booking error: " . $e->getMessage());
    $_SESSION["bookingError"] = "Booking #$id could not be deleted. Please try again or check the server error log.";
}

header("Location: ../View/dashboard.php");
exit();
?>

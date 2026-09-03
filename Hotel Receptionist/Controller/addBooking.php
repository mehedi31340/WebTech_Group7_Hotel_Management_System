<?php
require_once "../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: ../View/login.php");
    exit();
}

$guest = trim($_POST["guest_name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$room_id = (int)($_POST["room_id"] ?? 0);
$checkin = $_POST["checkin"] ?? "";
$checkout = $_POST["checkout"] ?? "";
$guests = (int)($_POST["guests"] ?? 1);
$request = trim($_POST["special_request"] ?? "");

// Which button was pressed: "checkin" = Book & Check In (pay now),
// "reserve" = Book Only (reserve the room, pay later at check-in).
$action = $_POST["action"] ?? "checkin";
if (!in_array($action, ["checkin", "reserve"], true)) {
    $action = "checkin";
}

$paymentMethod = $_POST["payment_method"] ?? "";
$allowedMethods = ["Cash", "Card", "Mobile Banking"];

if ($guest === "" || $phone === "" || $room_id <= 0 || $checkin === "" || $checkout === "") {
    $_SESSION["bookingError"] = "Please fill in all required fields.";
    header("Location: ../View/receptionist/booking.php");
    exit();
}

if ($guests < 1) {
    $_SESSION["bookingError"] = "Number of guests must be at least 1.";
    header("Location: ../View/receptionist/booking.php");
    exit();
}

// Guests must pay at check-in, so Book & Check In can't go through without
// a payment method. Book Only skips this — payment happens later, when the
// guest is actually checked in.
if ($action === "checkin" && !in_array($paymentMethod, $allowedMethods, true)) {
    $_SESSION["bookingError"] = "Select a payment method to book and check in the guest.";
    header("Location: ../View/receptionist/booking.php");
    exit();
}

$in = DateTime::createFromFormat("Y-m-d", $checkin);
$out = DateTime::createFromFormat("Y-m-d", $checkout);

if (!$in || !$out || $in->format("Y-m-d") !== $checkin || $out->format("Y-m-d") !== $checkout || $out <= $in) {
    $_SESSION["bookingError"] = "Check-out date must be after check-in date.";
    header("Location: ../View/receptionist/booking.php");
    exit();
}

try {
    // Make sure the selected room is really available.
    $roomStmt = mysqli_prepare($conn, "SELECT id, price FROM rooms WHERE id = ? AND status = 'Available' LIMIT 1");
    mysqli_stmt_bind_param($roomStmt, "i", $room_id);
    mysqli_stmt_execute($roomStmt);
    $roomResult = mysqli_stmt_get_result($roomStmt);
    $room = mysqli_fetch_assoc($roomResult);

    if (!$room) {
        $_SESSION["bookingError"] = "The selected room is not available. Please select another room.";
        header("Location: ../View/receptionist/booking.php");
        exit();
    }

    $days = (int)$in->diff($out)->days;
    $total = (float)$room["price"] * $days;

    // Book & Check In occupies the room and checks the guest in right away.
    // Book Only reserves the room (so it can't be double-booked) without
    // occupying or checking anyone in yet.
    $bookingStatus = $action === "checkin" ? "Checked In" : "Booked";
    $roomStatus = $action === "checkin" ? "Occupied" : "Reserved";

    mysqli_begin_transaction($conn);

    // Create the booking.
    $bookingStmt = mysqli_prepare(
        $conn,
        "INSERT INTO bookings
        (guest_name, phone, room_id, checkin, checkout, guests, total, status, source, special_request)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'walk_in', ?)"
    );
    mysqli_stmt_bind_param(
        $bookingStmt,
        "ssissidss",
        $guest,
        $phone,
        $room_id,
        $checkin,
        $checkout,
        $guests,
        $total,
        $bookingStatus,
        $request
    );
    mysqli_stmt_execute($bookingStmt);
    $booking_id = mysqli_insert_id($conn);

    // Create the bill (pending). ensureBilling() would do the same thing,
    // but doing it explicitly here keeps the initial base_amount in sync
    // with what was just quoted.
    $billStmt = mysqli_prepare(
        $conn,
        "INSERT INTO billing
        (booking_id, base_amount, extras_amount, discount_amount, total_amount, payment_status)
        VALUES (?, ?, 0, 0, ?, 'pending')"
    );
    mysqli_stmt_bind_param($billStmt, "idd", $booking_id, $total, $total);
    mysqli_stmt_execute($billStmt);

    // Guests must pay at check-in. Book Only leaves the bill pending
    // ("Payment Required") until the guest actually checks in.
    $charged = null;
    if ($action === "checkin") {
        $charged = markBillingPaid($conn, $booking_id, $paymentMethod);
    }

    $roomUpdate = mysqli_prepare($conn, "UPDATE rooms SET status = ? WHERE id = ? AND status = 'Available'");
    mysqli_stmt_bind_param($roomUpdate, "si", $roomStatus, $room_id);
    mysqli_stmt_execute($roomUpdate);

    if (mysqli_stmt_affected_rows($roomUpdate) !== 1) {
        throw new Exception("The room became unavailable.");
    }

    mysqli_commit($conn);

    if ($action === "checkin") {
        $_SESSION["bookingMessage"] = "Booking #" . $booking_id . " created and guest checked in. Payment of " . $charged . " BDT recorded ($paymentMethod).";
    } else {
        $_SESSION["bookingMessage"] = "Booking #" . $booking_id . " reserved successfully. The guest has not been checked in yet; payment will be collected at check-in.";
    }

    header("Location: ../View/dashboard.php");
    exit();
} catch (Throwable $e) {
    if ($conn->thread_id) {
        try { mysqli_rollback($conn); } catch (Throwable $ignored) {}
    }

    // Keep the user-facing message simple; log the technical reason for debugging.
    error_log("Hotel booking error: " . $e->getMessage());
    $_SESSION["bookingError"] = "Booking could not be completed. Please check the database and try again.";
    header("Location: ../View/receptionist/booking.php");
    exit();
}
?>

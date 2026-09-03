<?php
require_once "../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: ../View/login.php");
    exit();
}

$id = (int)($_POST["booking_id"] ?? 0);
$type = $_POST["request_type"] ?? "";
$time = $_POST["requested_time"] ?? "";

if ($id && in_array($type, ["early_checkin", "late_checkout"], true) && $time) {

    $check = mysqli_prepare($conn, "SELECT id FROM bookings WHERE id = ? AND status = 'Checked In' LIMIT 1");
    mysqli_stmt_bind_param($check, "i", $id);
    mysqli_stmt_execute($check);

    if (mysqli_num_rows(mysqli_stmt_get_result($check)) !== 1) {
        $_SESSION["earlyLateError"] = "No checked-in booking found with ID #$id.";
        header("Location: ../View/dashboard.php");
        exit();
    }

    $s = mysqli_prepare($conn, "INSERT INTO early_late_requests (booking_id,request_type,requested_time) VALUES (?,?,?)");
    mysqli_stmt_bind_param($s, "iss", $id, $type, $time);
    mysqli_stmt_execute($s);

    $_SESSION["earlyLateMessage"] = "Request submitted for booking #$id.";
} else {
    $_SESSION["earlyLateError"] = "Please choose a booking, request type, and time.";
}

header("Location: ../View/dashboard.php");
exit();
?>

<?php
require_once "../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: ../View/login.php");
    exit();
}

$id = (int)($_GET["id"] ?? 0);
$status = $_GET["status"] ?? "";

if (in_array($status, ["approved", "declined"], true)) {
    $s = mysqli_prepare($conn, "UPDATE early_late_requests SET status=? WHERE id=?");
    mysqli_stmt_bind_param($s, "si", $status, $id);
    mysqli_stmt_execute($s);

    $_SESSION["earlyLateMessage"] = "Request #$id $status.";
} else {
    $_SESSION["earlyLateError"] = "Invalid request status update.";
}

header("Location: ../View/dashboard.php");
exit();
?>

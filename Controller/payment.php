<?php
require_once "../config/database.php";
if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") { header("Location: ../View/login.php"); exit(); }

$id=(int)($_POST["booking_id"]??0); $method=$_POST["payment_method"]??"Cash";
$allowed=["Cash","Card","Mobile Banking"]; if(!in_array($method,$allowed,true)) $method="Cash";

$stmt=mysqli_prepare($conn,"SELECT id FROM bookings WHERE id=? LIMIT 1"); mysqli_stmt_bind_param($stmt,"i",$id); mysqli_stmt_execute($stmt);
$row=mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
if(!$row){ $_SESSION["paymentError"]="Booking not found."; header("Location: ../View/dashboard.php"); exit(); }

// Bring the bill up to date (room total + any service extras - discount)
// before charging it, so payment always matches what's actually owed.
recalcBilling($conn, $id);

$billStmt = mysqli_prepare($conn, "SELECT total_amount FROM billing WHERE booking_id = ?");
mysqli_stmt_bind_param($billStmt, "i", $id);
mysqli_stmt_execute($billStmt);
$bill = mysqli_fetch_assoc(mysqli_stmt_get_result($billStmt));

$s=mysqli_prepare($conn,"UPDATE billing SET payment_method = ?, payment_status = 'paid', paid_at = NOW() WHERE booking_id = ?");
mysqli_stmt_bind_param($s,"si",$method,$id); mysqli_stmt_execute($s);

$_SESSION["paymentMessage"]="Payment of " . $bill["total_amount"] . " BDT recorded successfully.";
header("Location: ../View/dashboard.php"); exit();
?>

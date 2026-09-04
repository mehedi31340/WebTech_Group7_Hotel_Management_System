<?php
require_once "../config/database.php";
if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") { header("Location: ../View/login.php"); exit(); }
$id=(int)($_POST["booking_id"]??0); $checkin=$_POST["checkin"]??""; $checkout=$_POST["checkout"]??"";
if(!$id||!$checkin||!$checkout||strtotime($checkout)<=strtotime($checkin)){ $_SESSION["modifyError"]="Please enter valid dates."; header("Location: ../View/dashboard.php"); exit(); }

$q=mysqli_prepare($conn,"SELECT r.price FROM bookings b JOIN rooms r ON b.room_id=r.id WHERE b.id=? AND b.status='Booked' LIMIT 1");
mysqli_stmt_bind_param($q,"i",$id); mysqli_stmt_execute($q); $row=mysqli_fetch_assoc(mysqli_stmt_get_result($q));
if(!$row){ $_SESSION["modifyError"]="Only booked reservations can be modified."; header("Location: ../View/dashboard.php"); exit(); }
$days=(int)((strtotime($checkout)-strtotime($checkin))/86400); $total=$row["price"]*$days;
$b=mysqli_prepare($conn,"UPDATE bookings SET checkin=?, checkout=?, total=? WHERE id=? AND status='Booked'");
mysqli_stmt_bind_param($b,"ssdi",$checkin,$checkout,$total,$id); mysqli_stmt_execute($b);
setBillingBase($conn, $id, $total);
$_SESSION["modifyMessage"]="Booking dates updated."; header("Location: ../View/dashboard.php"); exit();
?>

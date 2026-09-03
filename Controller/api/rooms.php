<?php
require_once "../../config/database.php";
header("Content-Type: application/json");
if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    http_response_code(403); echo json_encode(["error"=>"Access denied"]); exit();
}
$result=mysqli_query($conn,"SELECT room_no,room_type,price,status FROM rooms ORDER BY room_no");
$rooms=[];
while($r=mysqli_fetch_assoc($result)) $rooms[]=$r;
echo json_encode($rooms);
?>

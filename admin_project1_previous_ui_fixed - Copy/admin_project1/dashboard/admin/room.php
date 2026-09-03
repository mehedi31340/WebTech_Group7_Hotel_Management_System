<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../config/DatabaseConnection.php';
$db = new DatabaseConnection();
$connection = $db->openConnection();
$result = $db->getAllRooms($connection);
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>ZELO - Rooms</title><link rel="stylesheet" href="assets/css/room.css"><script src="assets/js/room.js" defer></script></head>
<body><div class="app"><?php include __DIR__ . '/_sidebar.php'; ?><main class="main"><div class="pageheader"><div><h2>Rooms</h2><span class="subtext">All rooms across <?= $result ? $result->num_rows : 0 ?> records</span></div><button class="btn-primary" type="button">+ Add room</button></div><div class="panel"><div class="panel-header"><h3>All rooms</h3><span class="filter-text">Filter by floor, type, status</span></div><table class="rooms-table"><thead><tr><th>ROOM</th><th>FLOOR</th><th>TYPE</th><th>STATUS</th><th></th></tr></thead><tbody><?php while($room=$result->fetch_assoc()): ?><tr><td><?= htmlspecialchars($room['room_number']) ?></td><td><?= (int)$room['floor'] ?></td><td><?= htmlspecialchars($room['type_name']) ?></td><td><span class="badge <?= htmlspecialchars($room['status']) ?>"><?= htmlspecialchars(ucfirst(str_replace('_',' ',$room['status']))) ?></span></td><td class="text-right"><button class="btn-action" type="button">Edit</button></td></tr><?php endwhile; ?></tbody></table><div class="table-footer-note">Rooms with no active bookings can be deleted from their edit panel.</div></div></main></div></body></html>

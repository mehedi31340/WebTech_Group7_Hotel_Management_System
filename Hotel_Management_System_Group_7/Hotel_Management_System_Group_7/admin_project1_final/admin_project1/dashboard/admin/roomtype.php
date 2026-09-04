<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../config/DatabaseConnection.php';
$db = new DatabaseConnection();
$connection = $db->openConnection();
$result = $db->getAllRoomTypes($connection);
$totalRoomTypes = $result ? $result->num_rows : 0;
?>
<!DOCTYPE html>
<html lang="en"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><title>ZELO - Room Types</title><link rel="stylesheet" href="assets/css/roomtype.css"><script src="assets/js/roomtype.js" defer></script></head>
<body><div class="app"><?php include __DIR__ . '/_sidebar.php'; ?><main class="main"><div class="pageheader"><div><h2>Room types</h2><span class="subtext"><?= $totalRoomTypes ?> types configured</span></div><button class="btn-primary" type="button">+ Add room type</button></div><div class="room-grid"><?php while($type=$result->fetch_assoc()): ?><div class="room-card"><div class="thumbnail"><?= $type['thumbnail_path'] ? '<img src="'.htmlspecialchars($type['thumbnail_path']).'" alt="">' : 'Thumbnail' ?></div><div class="card-body"><h3><?= htmlspecialchars($type['name']) ?></h3><div class="price">$<?= number_format((float)$type['price_per_night'],2) ?> <span>/ night</span></div><div class="specs">Sleeps <?= (int)$type['max_capacity'] ?> · <?= htmlspecialchars($type['amenities'] ?? 'Wi-Fi, AC, TV') ?></div><div class="actions"><button class="btn-action" type="button">Edit</button><button class="btn-action" type="button">Delete</button></div></div></div><?php endwhile; ?></div><div class="editpanel"><h3>Edit room type</h3><div class="form-row"><div class="form-group"><label>Name</label><input type="text" value=""></div><div class="form-group"><label>Price per night</label><input type="text" value=""></div></div></div></main></div></body></html>

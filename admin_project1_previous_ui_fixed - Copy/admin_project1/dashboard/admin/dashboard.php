<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../config/DatabaseConnection.php';
$db = new DatabaseConnection();
$connection = $db->openConnection();

$stats = $connection->query("SELECT
  (SELECT COUNT(*) FROM rooms WHERE status='occupied') AS occupied,
  (SELECT COUNT(*) FROM rooms) AS total_rooms,
  (SELECT COUNT(*) FROM rooms WHERE status='available') AS available_rooms,
  (SELECT COUNT(*) FROM rooms WHERE status='maintenance') AS maintenance_rooms,
  (SELECT COUNT(*) FROM reviews WHERE admin_reply IS NULL OR admin_reply='') AS pending_reviews,
  COALESCE((SELECT SUM(DATEDIFF(check_out, check_in) * rt.price_per_night) FROM bookings b JOIN rooms r ON r.id=b.room_id JOIN room_types rt ON rt.id=r.room_type_id WHERE b.status IN ('confirmed','completed') AND CURDATE() BETWEEN b.check_in AND DATE_SUB(b.check_out, INTERVAL 1 DAY)),0) AS today_revenue")->fetch_assoc();
$occupancy = (int)$stats['total_rooms'] > 0 ? round(((int)$stats['occupied'] / (int)$stats['total_rooms']) * 100) : 0;
$recentBookings = $db->getAllBookings($connection);
$recentReviews = $db->getAllReviews($connection);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>ZELO - Dashboard</title>
<link rel="stylesheet" href="assets/css/dashboard.css">
<script src="assets/js/dashboard.js" defer></script>
</head>
<body>
<div class="app">
<?php include __DIR__ . '/_sidebar.php'; ?>
<main class="main">
  <div class="pageheader">
    <div><h2>Dashboard</h2></div>
    <input class="searchbox" id="dashboardSearch" type="text" placeholder="Search rooms, bookings, guests...">
  </div>
  <div class="stats-row">
    <div class="card"><div class="label">Occupancy</div><div class="value"><?= $occupancy ?>%</div><div class="change"><?= (int)$stats['occupied'] ?> occupied</div></div>
    <div class="card"><div class="label">Today's revenue</div><div class="value">$<?= number_format((float)$stats['today_revenue'], 2) ?></div><div class="change">Live database total</div></div>
    <div class="card"><div class="label">Rooms available</div><div class="value"><?= (int)$stats['available_rooms'] ?> / <?= (int)$stats['total_rooms'] ?></div><div class="change" style="color:#6d7488;"><?= (int)$stats['occupied'] ?> occupied</div></div>
    <div class="card"><div class="label">Maintenance issues</div><div class="value"><?= (int)$stats['maintenance_rooms'] ?></div><div class="warn">Current room status</div></div>
    <div class="card"><div class="label">Pending reviews</div><div class="value"><?= (int)$stats['pending_reviews'] ?></div><div class="warn">awaiting reply</div></div>
  </div>
  <div class="panel">
    <div class="panel-header"><h3>Floor occupancy map</h3><a href="room.php">Live room status</a></div>
    <div class="floor-map">
      <?php $rooms = $db->getAllRooms($connection); $currentFloor = null; while ($room = $rooms->fetch_assoc()): if ($currentFloor !== $room['floor']): if ($currentFloor !== null): ?></div><?php endif; $currentFloor = $room['floor']; ?><div class="floor-row"><strong>Floor <?= (int)$currentFloor ?></strong><?php endif; ?><span class="room-cell <?= htmlspecialchars($room['status']) ?>" title="Room <?= htmlspecialchars($room['room_number']) ?> - <?= htmlspecialchars($room['status']) ?>"><?= htmlspecialchars($room['room_number']) ?></span><?php endwhile; if ($currentFloor !== null): ?></div><?php endif; ?>
    </div>
    <div class="legend">
      <span><span class="legend-dot" style="background:#1b2030;"></span>Occupied</span>
      <span><span class="legend-dot" style="background:#e3f3e8;"></span>Available</span>
      <span><span class="legend-dot" style="background:#fbead0;"></span>Maintenance</span>
      <span><span class="legend-dot" style="background:#f1efe9;"></span>Blocked</span>
    </div>
  </div>
  <div class="two-col">
    <div class="panel">
      <div class="panel-header"><h3>Recent bookings</h3><a href="booking.php">View all →</a></div>
      <table id="recentBookings"><thead><tr><th>Guest</th><th>Room</th><th>Dates</th><th>Source</th><th>Status</th></tr></thead><tbody>
      <?php $n=0; while ($b = $recentBookings->fetch_assoc()): if ($n++ >= 5) break; ?><tr><td><?= htmlspecialchars($b['guest_name']) ?></td><td><?= htmlspecialchars($b['room_number'] ?? '-') ?> · <?= htmlspecialchars($b['room_type'] ?? '-') ?></td><td><?= date('M j', strtotime($b['check_in'])) ?>–<?= date('M j', strtotime($b['check_out'])) ?></td><td><?= htmlspecialchars(ucfirst($b['source'])) ?></td><td><span class="status-pill"><?= htmlspecialchars($b['status']) ?></span></td></tr><?php endwhile; ?>
      </tbody></table>
    </div>
    <div class="panel">
      <div class="panel-header"><h3>Guest reviews queue</h3><a href="review.php"><?= (int)$stats['pending_reviews'] ?> pending</a></div>
      <?php $n=0; while ($r = $recentReviews->fetch_assoc()): if ($n++ >= 3) break; ?><div class="review-item"><strong><?= htmlspecialchars($r['guest_name']) ?></strong><p><?= htmlspecialchars($r['review_text'] ?? '') ?></p><small>Room <?= htmlspecialchars($r['room_number'] ?? '-') ?> · <?= htmlspecialchars($r['room_type'] ?? '-') ?> · <?= date('M j', strtotime($r['created_at'])) ?></small></div><?php endwhile; ?>
    </div>
  </div>
</main>
</div>
<style>
.floor-map{display:flex;flex-direction:column;gap:10px}.floor-row{display:flex;align-items:center;gap:7px;flex-wrap:wrap}.floor-row strong{width:70px;font-size:12px;color:#6d7488}.room-cell{min-width:38px;padding:8px 5px;text-align:center;border-radius:5px;font-size:11px;border:1px solid #ddd}.room-cell.occupied{background:#1b2030;color:#fff}.room-cell.available{background:#e3f3e8}.room-cell.maintenance{background:#fbead0}.room-cell.blocked{background:#f1efe9}.room-cell.dirty,.room-cell.in_progress{background:#f1efe9}
</style>
</body>
</html>

<?php
require_once __DIR__ . '/_auth.php';
require_once __DIR__ . '/../config/DatabaseConnection.php';
$db = new DatabaseConnection();
$connection = $db->openConnection();
$result = $db->getFinancialReport($connection);
$today = $connection->query("SELECT COALESCE(SUM(DATEDIFF(b.check_out,b.check_in)*rt.price_per_night),0) revenue FROM bookings b JOIN rooms r ON r.id=b.room_id JOIN room_types rt ON rt.id=r.room_type_id WHERE b.status IN ('confirmed','completed') AND b.check_in=CURDATE()")->fetch_assoc()['revenue'] ?? 0;
$week = $connection->query("SELECT COALESCE(SUM(DATEDIFF(b.check_out,b.check_in)*rt.price_per_night),0) revenue FROM bookings b JOIN rooms r ON r.id=b.room_id JOIN room_types rt ON rt.id=r.room_type_id WHERE b.status IN ('confirmed','completed') AND YEARWEEK(b.check_in,1)=YEARWEEK(CURDATE(),1)")->fetch_assoc()['revenue'] ?? 0;
$month = $connection->query("SELECT COALESCE(SUM(DATEDIFF(b.check_out,b.check_in)*rt.price_per_night),0) revenue FROM bookings b JOIN rooms r ON r.id=b.room_id JOIN room_types rt ON rt.id=r.room_type_id WHERE b.status IN ('confirmed','completed') AND YEAR(b.check_in)=YEAR(CURDATE()) AND MONTH(b.check_in)=MONTH(CURDATE())")->fetch_assoc()['revenue'] ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
    <head><meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ZELO - Financial Reports</title>
    <link rel="stylesheet" href="assets/css/financial.css">
    <script src="assets/js/financial.js" defer></script>
</head>
    <body><div class="app">
        <?php include __DIR__ . '/_sidebar.php'; ?>
        <main class="main">
            <div class="pageheader">
                <div>
                    <h2>Financial reports</h2>
                    <span class="subtext">Revenue by period and room type</span>
                </div>
                <button class="btn-export" type="button" onclick="window.print()">Export as HTML</button>
            </div><div class="stats-row">
                <div class="stat-item">
                    <div class="stat-score">$<?= number_format((float)$today,2) ?></div>
                    <div class="stat-label">Revenue today</div>
                </div>
                <div class="stat-item">
                    <div class="stat-score">$<?= number_format((float)$week,2) ?></div>
                    <div class="stat-label">Revenue this week</div>
                </div>
                <div class="stat-item">
                    <div class="stat-score">$<?= number_format((float)$month,2) ?></div>
                    <div class="stat-label">Revenue this month</div>
                </div>
            </div>
            <div class="panel">
                <div class="panel-header">
                    <h3>Revenue by room type</h3>
                </div>
                <table class="financial-table">
                    <thead>
                        <tr>
                            <th>ROOM TYPE</th>
                            <th>BOOKINGS</th>
                            <th>REVENUE</th>
                            <th>SHARE</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $rows=[];$total=0; while($r=$result->fetch_assoc()){ $rows[]=$r;$total+=(float)$r['revenue']; } foreach($rows as $r): ?>
                        <tr>
                            <td><?= htmlspecialchars($r['room_type']) ?></td>
                            <td><?= (int)$r['total_bookings'] ?></td>
                            <td>$<?= number_format((float)$r['revenue'],2) ?></td>
                            <td><?= $total>0 ? round(((float)$r['revenue']/$total)*100) : 0 ?>%</td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <div class="table-footer-note">Includes room revenue from confirmed and completed bookings.</div>
            </div>
        </main>
    </div>
</body>
</html>

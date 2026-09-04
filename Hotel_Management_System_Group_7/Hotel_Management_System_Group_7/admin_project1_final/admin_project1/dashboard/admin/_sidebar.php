<?php
$currentPage = basename($_SERVER['PHP_SELF']);
$active = static function (string $file) use ($currentPage): string {
    return $currentPage === $file ? ' active' : '';
};
?>
<aside class="sidebar">
  <div class="brand">
    <h1>ZELO</h1>
    <span>ADMIN CONSOLE</span>
  </div>
  <div class="navlabel">Overview</div>
  <button class="navbtn<?= $active('dashboard.php') ?>" onclick="showPage('dashboard', this)">Dashboard</button>
  <div class="navlabel">Inventory</div>
  <button class="navbtn<?= $active('roomtype.php') ?>" onclick="showPage('room-types', this)">Room types</button>
  <button class="navbtn<?= $active('room.php') ?>" onclick="showPage('rooms', this)">Rooms</button>
  <button class="navbtn" onclick="showPage('rooms', this)">Seasonal pricing</button>
  <div class="navlabel">People</div>
  <button class="navbtn" onclick="showPage('dashboard', this)">Staff accounts</button>
  <button class="navbtn" onclick="showPage('dashboard', this)">Guest accounts</button>
  <div class="navlabel">Operations</div>
  <button class="navbtn<?= $active('booking.php') ?>" onclick="showPage('bookings', this)">Bookings</button>
  <button class="navbtn<?= $active('review.php') ?>" onclick="showPage('reviews', this)">Reviews</button>
  <div class="navlabel">Reports</div>
  <button class="navbtn<?= $active('financial.php') ?>" onclick="showPage('financial', this)">Financial</button>
  <div class="footer">
    <div class="avatar">JH</div>
    <div>
      <div style="color:#fff; font-size:14px; font-weight:600;"><?= htmlspecialchars($adminName) ?></div>
      <div style="font-size:12px; color:#8b93a8;"><?= htmlspecialchars(ucfirst($adminRole)) ?></div>
    </div>
  </div>
</aside>

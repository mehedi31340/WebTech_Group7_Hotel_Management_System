<?php
session_start();

if (empty($_SESSION["isLoggedIn"])) {
    header("Location: ../auth/View/login.php");
    exit;
}




require_once "../config/DatabaseConnection.php";

$db = new DatabaseConnection();

$connection = $db->openConnection();

$result = $db->getFinancialReport($connection);

?>

<!DOCTYPE html>
<head>
  <title>ZELO - Rooms</title>
  <link rel="stylesheet" href="style4.css">
  <script src="script1.js"></script>
</head>
<body>
  <div class="app">
   
    <aside class="sidebar">
      <div class="brand">
        <h1>ZELO</h1>
        <span>ADMIN CONSOLE</span>
      </div>

      <div class="navlabel">Overview</div>
      <button class="navbtn" onclick="showPage('dashboard', this)">Dashboard</button>

      <div class="navlabel">Inventory</div>
      <button class="navbtn" onclick="showPage('room-types', this)">Room types</button>
      <button class="navbtn " onclick="showPage('rooms', this)">Rooms</button>
      <button class="navbtn" onclick="showPage('seasonal-pricing', this)">Seasonal pricing</button>

      <div class="navlabel">People</div>
      <button class="navbtn" onclick="showPage('staff-accounts', this)">Staff accounts</button>
      <button class="navbtn" onclick="showPage('guest-accounts', this)">Guest accounts</button>

      <div class="navlabel">Operations</div>
      <button class="navbtn" onclick="showPage('bookings', this)">Bookings</button>
      <button class="navbtn " onclick="showPage('reviews', this)">Reviews</button>

      <div class="navlabel">Reports</div>
      <button class="navbtn active" onclick="showPage('financial', this)">Financial</button>

      <div class="footer">
        <div class="avatar">JH</div>
        <div>
          <div style="color:#fff; font-size:14px; font-weight:600;">JUBAIR HOSSAIN</div>
          <div style="font-size:12px; color:#8b93a8;">Admin</div>
        </div>
      </div>
    </aside>
    <main class="main">
      <div class="pageheader">
        <div>
          <h2>Financial reports</h2>
          <span class="subtext">Revenue by period and room type</span>
        </div>
        <button class="btn-export">Export as HTML</button>
      </div>

      <div class="stats-row">
        <div class="stat-item">
          <div class="stat-score">$18,240</div>
          <div class="stat-label">Revenue today</div>
        </div>
        <div class="stat-item">
          <div class="stat-score">$121,860</div>
          <div class="stat-label">Revenue this week</div>
        </div>
        <div class="stat-item">
          <div class="stat-score">$498,300</div>
          <div class="stat-label">Revenue this month</div>
        </div>
      </div>

      <!-- Financial Data Table Panel -->
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
            <tr>
              <td>Deluxe</td>
              <td>412</td>
              <td>$187,400</td>
              <td>38%</td>
            </tr>
            <tr>
              <td>Suite</td>
              <td>168</td>
              <td>$142,600</td>
              <td>29%</td>
            </tr>
            <tr>
              <td>Standard</td>
              <td>390</td>
              <td>$98,100</td>
              <td>20%</td>
            </tr>
            <tr>
              <td>Executive</td>
              <td>121</td>
              <td>$70,200</td>
              <td>13%</td>
            </tr>
          </tbody>
        </table>

        <div class="table-footer-note">
          Includes extras and service revenue.
        </div>
      </div>
    </main>
  </div>
</body>
</html>


<?php
session_start();

if (empty($_SESSION["isLoggedIn"])) {
    header("Location: ../auth/View/login.php");
    exit;
}


require_once "../config/DatabaseConnection.php";
$db = new DatabaseConnection();

$connection = $db->openConnection();

$result = $db->getAllRooms($connection);
?>
<!DOCTYPE html>
<head>
  <title>ZELO - Rooms</title>
  <link rel="stylesheet" href="style2.css">
   <script src="script3.js"></script>
</head>
<body>
  <div class="app">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="brand">
        <h1>ZELO</h1>
        <span>ADMIN CONSOLE</span>
      </div>

      <div class="navlabel">Overview</div>
      <button class="navbtn" onclick="showPage('dashboard', this)">Dashboard</button>

      <div class="navlabel">Inventory</div>
      <button class="navbtn" onclick="showPage('room-types', this)">Room types</button>
      <button class="navbtn active" onclick="showPage('rooms', this)">Rooms</button>
      <button class="navbtn" onclick="showPage('seasonal-pricing', this)">Seasonal pricing</button>

      <div class="navlabel">People</div>
      <button class="navbtn" onclick="showPage('staff-accounts', this)">Staff accounts</button>
      <button class="navbtn" onclick="showPage('guest-accounts', this)">Guest accounts</button>

      <div class="navlabel">Operations</div>
      <button class="navbtn" onclick="showPage('bookings', this)">Bookings</button>
      <button class="navbtn" onclick="showPage('reviews', this)">Reviews</button>

      <div class="navlabel">Reports</div>
      <button class="navbtn" onclick="showPage('financial', this)">Financial</button>

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
          <h2>Rooms</h2>
          <span class="subtext">124 rooms across 5 floors</span>
        </div>
        <button class="btn-primary">+ Add room</button>
      </div>

      <div class="panel">
        <div class="panel-header">
          <h3>All rooms</h3>
          <span class="filter-text">Filter by floor, type, status</span>
        </div>

        <table class="rooms-table">
          <thead>
            <tr>
              <th>ROOM</th>
              <th>FLOOR</th>
              <th>TYPE</th>
              <th>STATUS</th>
              <th></th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>101</td>
              <td>1</td>
              <td>Standard</td>
              <td><span class="badge available">Available</span></td>
              <td class="text-right"><button class="btn-action">Edit</button></td>
            </tr>
            <tr>
              <td>203</td>
              <td>2</td>
              <td>Deluxe</td>
              <td><span class="badge occupied">Occupied</span></td>
              <td class="text-right"><button class="btn-action">Edit</button></td>
            </tr>
            <tr>
              <td>312</td>
              <td>3</td>
              <td>Deluxe</td>
              <td><span class="badge occupied">Occupied</span></td>
              <td class="text-right"><button class="btn-action">Edit</button></td>
            </tr>
            <tr>
              <td>407</td>
              <td>4</td>
              <td>Suite</td>
              <td><span class="badge maintenance">Maintenance</span></td>
              <td class="text-right"><button class="btn-action">Edit</button></td>
            </tr>
            <tr>
              <td>502</td>
              <td>5</td>
              <td>Executive</td>
              <td><span class="badge blocked">Blocked</span></td>
              <td class="text-right"><button class="btn-action">Edit</button></td>
            </tr>
          </tbody>
        </table>

        <div class="table-footer-note">
          Rooms with no active bookings can be deleted from their edit panel.
        </div>
      </div>
    </main>
  </div>
</body>
</html>
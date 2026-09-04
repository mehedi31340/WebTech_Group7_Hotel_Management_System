<?php
session_start();

if (empty($_SESSION["isLoggedIn"])) {
    header("Location: ../auth/View/login.php");
    exit;
}


require_once "../config/DatabaseConnection.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
  //<meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Meridian - Bookings</title>
  <link rel="stylesheet" href="style5.css">
  <script src="script5.js" defer></script>
</head>
<body>
<div class="app">

  <aside class="sidebar">
    <div class="brand">
      <h1>zelo</h1>
      <span>ADMIN CONSOLE</span>
    </div>

    <div class="sidebar-scroll">
      <div class="navlabel">Overview</div>
      <button class="navbtn" onclick="showPage('dashboard',this)">Dashboard</button>

      <div class="navlabel">Inventory</div>
      <button class="navbtn" onclick="showPage('room-types',this)">Room types</button>
      <button class="navbtn" onclick="showPage('rooms',this)">Rooms</button>
      <button class="navbtn" onclick="showPage('seasonal-pricing',this)">Seasonal pricing</button>

      <div class="navlabel">People</div>
      <button class="navbtn" onclick="showPage('staff-accounts',this)">Staff accounts</button>
      <button class="navbtn" onclick="showPage('guest-accounts',this)">Guest accounts</button>

      <div class="navlabel">Operations</div>
      <button class="navbtn active" onclick="showPage('bookings',this)">Bookings</button>
      <button class="navbtn" onclick="showPage('reviews',this)">Reviews</button>
      <button class="navbtn" onclick="showPage('complaints',this)">Complaints</button>
      <button class="navbtn" onclick="showPage('announcements',this)">Announcements</button>

      <div class="navlabel">Reports</div>
      <button class="navbtn" onclick="showPage('financial',this)">Financial</button>
    </div>

    <div class="footer">
      <div class="avatar">JH</div>
      <div>
        <div class="admin-name">JUBAIR HOSSAIN</div>
        <div class="admin-role">Admin</div>
      </div>
    </div>
  </aside>

  <main class="main">
    <header class="pageheader">
      <div>
        <h2>Bookings</h2>
        <span class="subtext">All bookings across the hotel</span>
      </div>
    </header>

    <section class="panel">
      <div class="panel-title">
        <h3>Filter</h3>
      </div>

      <div class="filters">
        <div class="filter-group">
          <label>Status</label>
          <select id="status" onchange="filterBookings()">
            <option value="all">All</option>
            <option value="confirmed">Confirmed</option>
            <option value="pending">Pending</option>
          </select>
        </div>

        <div class="filter-group">
          <label>Date range</label>
          <input type="text" id="dateRange" value="Aug 1 – Aug 31, 2026">
        </div>

        <div class="filter-group">
          <label>Room type</label>
          <select id="roomType" onchange="filterBookings()">
            <option value="all">All types</option>
            <option value="standard">Standard</option>
            <option value="deluxe">Deluxe</option>
            <option value="suite">Suite</option>
          </select>
        </div>

        <div class="filter-group">
          <label>Source</label>
          <select id="source" onchange="filterBookings()">
            <option value="all">All sources</option>
            <option value="online">Online</option>
            <option value="walk-in">Walk-in</option>
          </select>
        </div>
      </div>

      <div class="table-wrapper">
        <table class="bookings-table">
          <thead>
            <tr>
              <th>GUEST</th>
              <th>ROOM</th>
              <th>DATES</th>
              <th>SOURCE</th>
              <th>STATUS</th>
            </tr>
          </thead>
          <tbody id="bookingTableBody">
            <tr data-status="confirmed" data-room="deluxe" data-source="online">
              <td>N. Farouk</td>
              <td>312</td>
              <td>Aug 19–22</td>
              <td><span class="source online">Online</span></td>
              <td><span class="status confirmed">Confirmed</span></td>
            </tr>
            <tr data-status="confirmed" data-room="standard" data-source="walk-in">
              <td>S. Okafor</td>
              <td>108</td>
              <td>Aug 18–19</td>
              <td><span class="source walkin">Walk-in</span></td>
              <td><span class="status confirmed">Confirmed</span></td>
            </tr>
            <tr data-status="pending" data-room="deluxe" data-source="online">
              <td>L. Tan</td>
              <td>415</td>
              <td>Aug 20–25</td>
              <td><span class="source online">Online</span></td>
              <td><span class="status pending">Pending</span></td>
            </tr>
          </tbody>
        </table>
      </div>
    </section>
  </main>
</div>
</body>
</html>
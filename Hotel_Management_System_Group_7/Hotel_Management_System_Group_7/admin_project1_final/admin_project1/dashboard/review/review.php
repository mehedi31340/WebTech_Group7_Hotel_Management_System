<?php
session_start();

if (empty($_SESSION["isLoggedIn"])) {
    header("Location: ../auth/View/login.php");
    exit;
}



require_once "../config/DatabaseConnection.php";

$db = new DatabaseConnection();

$connection = $db->openConnection();

$result = $db->getAllReviews($connection);

?>
<!DOCTYPE html>
<head>
  <title>ZELO - Rooms</title>
  <link rel="stylesheet" href="style3.css">
  <script src="script2.js"></script>
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
      <button class="navbtn active" onclick="showPage('reviews', this)">Reviews</button>
    

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
      <h2>Guest reviews</h2>
      <span class="subtext">Average rating 4.6/ 5</span>
    </div>
  </div>

  
  <div class="stats-row">
    <div class="stat-item">
      <div class="stat-score">4.5</div>
      <div class="stat-label">Cleanliness</div>
    </div>
    <div class="stat-item">
      <div class="stat-score">4.2</div>
      <div class="stat-label">Service</div>
    </div>
    <div class="stat-item">
      <div class="stat-score">4.4</div>
      <div class="stat-label">Overall</div>
    </div>
  </div>

  <!-- Reviews Container Panel -->
  <div class="panel">
    <div class="panel-header">
      <h3>Recent reviews</h3>
    </div>

    <div class="reviews-list">
     
      <div class="review-card">
        <div class="review-header">
          <span class="reviewer-name">A. Kabir</span>
          <div class="stars">4.9/5</div>
        </div>
        <p class="review-text">"Lovely stay overall, but the AC in our room took a while to cool down."</p>
        <div class="review-meta">Room 221 · Deluxe · Aug 14</div>
        <a href="#" class="reply-link">Post official reply →</a>
      </div>

      
      <div class="review-card">
        <div class="review-header">
          <span class="reviewer-name">urmila</span>
          <div class="stars">4.5/5</div>
        </div>
        <p class="review-text">"Front desk was slow at check-in. Room itself was clean and quiet."</p>
        <div class="review-meta">Room 118 · Standard · Aug 15</div>
        <a href="#" class="reply-link">Post official reply →</a>
      </div>

      
      <div class="review-card">
        <div class="review-header">
          <span class="reviewer-name">rakib</span>
          <div class="stars">5/5</div>
        </div>
        <p class="review-text">"Perfect suite, incredible view, staff went out of their way to help."</p>
        <div class="review-meta">Room 415 · Suite · Aug 12</div>
        <span class="replied-status">Replied ✓</span>
      </div>
    </div>
  </div>
</main>
  </div>
</body>
</html>
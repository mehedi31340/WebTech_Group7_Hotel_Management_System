<?php
session_start();

if (empty($_SESSION["isLoggedIn"])) {
    header("Location: ../auth/View/login.php");
    exit;
}



require_once "../config/DatabaseConnection.php";

$db = new DatabaseConnection();

$connection = $db->openConnection();

$result = $db->getAllRoomTypes($connection);

$totalRoomTypes = $result->num_rows;

?>
<!DOCTYPE html>
<head>
    <title>ZELO-Room Types</title>
    <link rel="stylesheet" href="style1.css">
     <script src="script4.js"></script>
    </head>
    <body>
    <div class="app">
    <aside class="sidebar">
        <div class="brand">
            <h1>ZELO</h1>
            <Span>ADMIN CONSOLE</Span>
            </div>
            <div class="navlabel">overview</div>
            <button class="navbtn " onclick="showPage('dashboard', this)">Dashboard</button>
            <div class="navlabel">Intentory</div>
            <button class="navbtn active" onclick="showPage('room-types', this)">Room types</button>
            <button class="navbtn" onclick="showPage('rooms', this)">Rooms</button>
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

            </div>
             <div style="color:#fff; font-size:14px;">JUBAIR HOSSAIN</div>
        <div style="font-size:12px; color:#8b93a8;">Admin</div>
      </div>
    </div>
  </aside>
<main class="main">
   <div class="pageheader">
    <div>
        <h2>Room types</h2>
        <span class="subtext">
            <?php echo $totalRoomTypes; ?> types configured
        </span>
    </div>

    <button class="btn-primary">+ Add room type</button>
</div>
      <div class="room-grid">
        <div class="room-card">
          <div class="thumbnail">Thumbnail</div>
          <div class="card-body">
            <h3>Standard</h3>
            <div class="price">$120 <span>/ night</span></div>
            <div class="specs">Sleeps 2 · Wi-Fi, AC, TV</div>
            <div class="actions">
              <button class="btn-action">Edit</button>
              <button class="btn-action">Delete</button>
            </div>
          </div>
        </div>
        <div class="room-card">
          <div class="thumbnail">Thumbnail</div>
          <div class="card-body">
            <h3>Standard</h3>
            <div class="price">$120 <span>/ night</span></div>
            <div class="specs">Sleeps 2 · Wi-Fi, AC, TV</div>
            <div class="actions">
              <button class="btn-action">Edit</button>
              <button class="btn-action">Delete</button>
            </div>
          </div>
        </div>

        <div class="room-card">
          <div class="thumbnail">Thumbnail</div>
          <div class="card-body">
            <h3>Suite</h3>
            <div class="price">$340 <span>/ night</span></div>
            <div class="specs">Sleeps 4 · Lounge, Balcony, Bathtub</div>
            <div class="actions">
              <button class="btn-action">Edit</button>
              <button class="btn-action">Delete</button>
            </div>
          </div>
        </div>
        <div class="room-card">
          <div class="thumbnail">Thumbnail</div>
          <div class="card-body">
            <h3>Executive</h3>
            <div class="price">$260 <span>/ night</span></div>
            <div class="specs">Sleeps 2 · Workspace, Lounge access</div>
            <div class="actions">
              <button class="btn-action">Edit</button>
              <button class="btn-action">Delete</button>
            </div>
          </div>
        </div>
      </div>
     <div class="editpanel">
        <h3>Edit room type — Deluxe</h3>
        <div class="form-row">
          <div class="form-group">
            <label>Name</label>
            <input type="text" value="Deluxe">
          </div>
          <div class="form-group">
            <label>Price per night</label>
            <input type="text" value="$185">
          </div>
        </div>
      </div>
    </main>
  </div>
</body>
</html>



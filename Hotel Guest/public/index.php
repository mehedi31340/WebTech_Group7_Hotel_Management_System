<?php
require_once "../config/database.php";

$page = $_GET['page'] ?? 'home';

if ($page === 'logout') {
    session_destroy();
    redirect("index.php");
}

if ($page === 'register' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $nationality = trim($_POST['nationality'] ?? '');
    $id_number = trim($_POST['id_number'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && $phone && $nationality && $id_number && strlen($password) >= 6) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO guests(full_name,email,phone,nationality,id_number,password_hash) VALUES(?,?,?,?,?,?)");
        $stmt->bind_param("ssssss", $name, $email, $phone, $nationality, $id_number, $hash);
        if ($stmt->execute()) {
            $_SESSION['guest_id'] = $stmt->insert_id;
            $_SESSION['guest_name'] = $name;
            redirect("index.php?page=dashboard");
        }
        $error = "Email or ID number may already be registered.";
    } else {
        $error = "Please fill every field correctly. Password must be at least 6 characters.";
    }
}

if ($page === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $stmt = $conn->prepare("SELECT id,full_name,password_hash FROM guests WHERE email=? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $user = $stmt->get_result()->fetch_assoc();
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['guest_id'] = $user['id'];
        $_SESSION['guest_name'] = $user['full_name'];
        redirect("index.php?page=dashboard");
    }
    $error = "Invalid email or password.";
}

if ($page === 'book' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    $room_type_id = (int)($_POST['room_type_id'] ?? 0);
    $checkin = $_POST['checkin'] ?? '';
    $checkout = $_POST['checkout'] ?? '';
    $guests = (int)($_POST['guests'] ?? 1);
    $special = trim($_POST['special_requests'] ?? '');

    $start = strtotime($checkin);
    $end = strtotime($checkout);
    if (!$room_type_id || !$start || !$end || $end <= $start || $guests < 1) {
        $error = "Please provide valid booking details.";
        $page = 'rooms';
    } else {
        $nights = (int)(($end - $start) / 86400);
        $stmt = $conn->prepare("SELECT id,name,price_per_night,capacity FROM room_types WHERE id=?");
        $stmt->bind_param("i", $room_type_id);
        $stmt->execute();
        $room = $stmt->get_result()->fetch_assoc();

        if (!$room || $guests > $room['capacity']) {
            $error = "Selected room cannot accommodate this number of guests.";
            $page = 'rooms';
        } else {
            $stmt = $conn->prepare("SELECT COUNT(*) c FROM bookings b WHERE b.room_type_id=? AND b.status IN('confirmed','upcoming') AND b.check_in < ? AND b.check_out > ?");
            $stmt->bind_param("iss", $room_type_id, $checkout, $checkin);
            $stmt->execute();
            $occupied = (int)$stmt->get_result()->fetch_assoc()['c'];

            if ($occupied >= 1) {
                $error = "That room type is not available for the selected dates.";
                $page = 'rooms';
            } else {
                $subtotal = $nights * (float)$room['price_per_night'];
                $stmt = $conn->prepare("SELECT COALESCE(SUM(points),0) balance FROM loyalty_transactions WHERE guest_id=?");
                $gid = guest_id();
                $stmt->bind_param("i", $gid);
                $stmt->execute();
                $points = (int)$stmt->get_result()->fetch_assoc()['balance'];
                $discount = min($points, floor($subtotal * 0.25));
                $total = max(0, $subtotal - $discount);

                $booking_code = "BK" . date("ymd") . strtoupper(substr(bin2hex(random_bytes(4)),0,6));
                $stmt = $conn->prepare("INSERT INTO bookings(guest_id,room_type_id,booking_code,check_in,check_out,guests,special_requests,subtotal,loyalty_discount,total_amount,status) VALUES(?,?,?,?,?,?,?,?,?,?,?)");
                $status = "confirmed";
                $stmt->bind_param("iisssisddds", $gid,$room_type_id,$booking_code,$checkin,$checkout,$guests,$special,$subtotal,$discount,$total,$status);
                if ($stmt->execute()) {
                    $booking_id = $stmt->insert_id;
                    if ($discount > 0) {
                        $desc = "Redeemed points for booking " . $booking_code;
                        $redeem = -(int)$discount;
                        $lt = $conn->prepare("INSERT INTO loyalty_transactions(guest_id,booking_id,points,description) VALUES(?,?,?,?)");
                        $lt->bind_param("iiis", $gid, $booking_id, $redeem, $desc);
                        $lt->execute();
                    }
                    $inv = "INV-" . date("Ymd") . "-" . str_pad($booking_id,4,"0",STR_PAD_LEFT);
                    $stmt2 = $conn->prepare("INSERT INTO invoices(booking_id,invoice_no,amount,payment_status) VALUES(?,?,?,'paid')");
                    $stmt2->bind_param("isd",$booking_id,$inv,$total);
                    $stmt2->execute();
                    redirect("index.php?page=confirmation&id=".$booking_id);
                }
                $error = "Could not create booking.";
            }
        }
    }
}

if ($page === 'cancel' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    $id = (int)$_POST['booking_id'];
    $stmt = $conn->prepare("UPDATE bookings SET status='cancelled' WHERE id=? AND guest_id=? AND status='confirmed' AND check_in > DATE_ADD(CURDATE(), INTERVAL 2 DAY)");
    $stmt->bind_param("ii",$id,$gid=guest_id());
    $stmt->execute();
    redirect("index.php?page=bookings");
}

if ($page === 'service' && $_SERVER['REQUEST_METHOD'] === 'POST') {

    require_login();

    // Logged-in guest ID
    $gid = guest_id();

    // Get form data
    $type = trim($_POST['service_type'] ?? 'Other');

    $qty = max(
        1,
        (int)($_POST['quantity'] ?? 1)
    );

    $notes = trim(
        $_POST['notes'] ?? ''
    );

    $booking_id = (int)(
        $_POST['booking_id'] ?? 0
    );


    // Validate booking
    if ($booking_id <= 0) {
        die("Please select a valid booking.");
    }


    // Insert service request
    $stmt = $conn->prepare("
        INSERT INTO service_requests
        (
            guest_id,
            booking_id,
            service_type,
            quantity,
            notes,
            status
        )
        VALUES (?, ?, ?, ?, ?, 'pending')
    ");

    if (!$stmt) {
        die("Database error: " . $conn->error);
    }


    // Bind variables
    $stmt->bind_param(
        "iisis",
        $gid,
        $booking_id,
        $type,
        $qty,
        $notes
    );


    // Execute
    if (!$stmt->execute()) {
        die("Service request failed: " . $stmt->error);
    }


    $stmt->close();


    // Back to Services page
    redirect("index.php?page=services");
}

if ($page === 'review' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    require_login();
    $booking_id = (int)$_POST['booking_id'];
    $overall = max(1,min(5,(int)$_POST['overall']));
    $clean = max(1,min(5,(int)$_POST['cleanliness']));
    $service = max(1,min(5,(int)$_POST['service']));
    $comment = trim($_POST['comment'] ?? '');
    $stmt = $conn->prepare("INSERT INTO reviews(guest_id,booking_id,overall_rating,cleanliness_rating,service_rating,comment) VALUES(?,?,?,?,?,?)");
    //$stmt->bind_param("iiiiis",$gid=guest_id(),$booking_id,$overall,$clean,$service,$comment);
    //$stmt->execute([guest_id(), $booking_id, $overall, $clean, $service, $comment]);
    //$stmt->execute();
    $gid = guest_id();

$stmt->bind_param(
    "iiiiis",
    $gid,
    $booking_id,
    $overall,
    $clean,
    $service,
    $comment
);

$stmt->execute();
    redirect("index.php?page=reviews");
}

function layout_start($title) {
    global $page;
    ?>
    <!doctype html><html lang="en"><head>
    <meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
    <title><?=e($title)?> · HotelGuest</title>
    <link rel="stylesheet" href="css/style.css">
    <script defer src="js/app.js"></script>
    </head><body>
    <header class="topbar"><a class="brand" href="index.php">Hotel<span>Guest</span></a>
    <nav>
      <?php if (!empty($_SESSION['guest_id'])): ?>
      <a href="index.php?page=dashboard">Dashboard</a><a href="index.php?page=rooms">Rooms</a><a href="index.php?page=bookings">Bookings</a><a href="index.php?page=services">Services</a><a href="index.php?page=reviews">Reviews</a><a href="index.php?page=billing">Billing</a><a href="index.php?page=profile">Profile</a><a class="nav-logout" href="index.php?page=logout">Logout</a>
      <?php else: ?><a href="index.php?page=login">Login</a><a class="btn small" href="index.php?page=register">Register</a><?php endif; ?>
    </nav></header><main class="container">
    <?php
}
function layout_end(){ echo '</main><footer>HotelGuest · Guest Reservation System · BDT</footer></body></html>'; }

if ($page === 'home') {
    layout_start("HotelGuest");
    ?>
    <section class="hero"><div><span class="eyebrow">HOTEL GUEST APP</span><h1>Book your perfect stay in Bangladesh.</h1><p>Search rooms, manage reservations, request services, earn loyalty points and keep every invoice in one place.</p><div class="actions"><a class="btn" href="index.php?page=rooms">Search Rooms</a><a class="btn ghost" href="index.php?page=register">Create Account</a></div></div><div class="hero-card"><div class="hotel-icon">🏨</div><strong>Guest Experience</strong><span>Simple · Fast · BDT</span></div></section>
    <section class="feature-grid">
      <div class="feature">🔎<h3>AJAX Room Search</h3><p>Dates and guests update room results without a full reload.</p></div>
      <div class="feature">🛏️<h3>Easy Booking</h3><p>See price, seasonal notices, discount and payment before confirmation.</p></div>
      <div class="feature">⭐<h3>Loyalty Rewards</h3><p>Earn points after completed stays and redeem them on your next booking.</p></div>
      <div class="feature">🧾<h3>Billing</h3><p>View invoices and printable receipt details.</p></div>
    </section>
    <?php layout_end(); exit;
}

if ($page === 'login' || $page === 'register') {
    layout_start($page === 'login' ? "Login" : "Register");
    ?>
    <div class="auth-card">
      <span class="eyebrow">HOTELGUEST</span><h1><?= $page==='login'?'Welcome Back':'Create Account' ?></h1>
      <?php if (!empty($error)): ?><div class="alert error"><?=e($error)?></div><?php endif; ?>
      <form method="post" class="form" enctype="multipart/form-data">
      <?php if ($page==='register'): ?>
        <label>Profile Picture<input type="file" name="profile_picture" accept="image/*"></label><label>Full Name<input name="full_name" required></label>
        <label>Email<input type="email" name="email" required></label>
        <label>Phone<input name="phone" required></label>
        <label>Nationality<input name="nationality" value="Bangladeshi" required></label>
        <label>ID Number<input name="id_number" required></label>
      <?php else: ?><label>Email<input type="email" name="email" required></label><?php endif; ?>
      <label>Password<input type="password" name="password" required></label>
      <button class="btn full"><?= $page==='login'?'Login':'Register' ?></button>
      </form>
      <p class="muted"><?= $page==='login'?"Don't have an account?":"Already have an account?" ?> <a href="index.php?page=<?=$page==='login'?'register':'login'?>"><?= $page==='login'?'Register':'Login' ?></a></p>
    </div>
    <?php layout_end(); exit;
}

require_login();

if ($page === 'dashboard') {
    layout_start("Dashboard");
    $gid=guest_id();
    $stmt=$conn->prepare("SELECT COALESCE(SUM(points),0) p FROM loyalty_transactions WHERE guest_id=?"); $stmt->bind_param("i",$gid); $stmt->execute(); $points=(int)$stmt->get_result()->fetch_assoc()['p'];
    $stmt=$conn->prepare("SELECT COUNT(*) c FROM bookings WHERE guest_id=? AND status IN('confirmed','upcoming')"); $stmt->bind_param("i",$gid); $stmt->execute(); $up=(int)$stmt->get_result()->fetch_assoc()['c'];
    ?>
    <div class="page-head"><div><span class="eyebrow">GUEST DASHBOARD</span><h1>Hi, <?=e($_SESSION['guest_name'])?> </h1><p>Everything for your stay, in one place.</p></div><a class="btn" href="index.php?page=rooms">Book a Room</a></div>
    <div class="stats"><div><span>Upcoming</span><strong><?=$up?></strong></div><div><span>Loyalty Points</span><strong><?=$points?></strong></div><div><span>Currency</span><strong>BDT</strong></div></div>
    <div class="dashboard-grid">
      <a class="dash-card" href="index.php?page=bookings"><b>📅 My Bookings</b><span>Upcoming, past, cancellation and modification</span></a>
      <a class="dash-card" href="index.php?page=services"><b>🛎️ In-Stay Services</b><span>Extra bed, laundry, toiletries, room service</span></a>
      <a class="dash-card" href="index.php?page=reviews"><b>⭐ Reviews</b><span>Rate completed stays and manage reviews</span></a>
      <a class="dash-card" href="index.php?page=billing"><b>🧾 Billing History</b><span>Invoices, payment status and receipt view</span></a>
    </div>
    <?php layout_end(); exit;
}

if ($page === 'rooms') {
    layout_start("Search Rooms");
    ?>
    <div class="page-head"><div><span class="eyebrow">ROOM SEARCH · AJAX</span><h1>Find an available room</h1><p>Results update without reloading the page.</p></div></div>
    <form id="searchForm" class="search-box">
      <label>Check-in<input type="date" name="checkin" required></label>
      <label>Check-out<input type="date" name="checkout" required></label>
      <label>Guests<input type="number" name="guests" min="1" value="2" required></label>
      <button class="btn">Search</button>
    </form>
    <div id="seasonNotice"></div><div id="roomResults" class="room-grid"><div class="empty">Choose dates and press Search.</div></div>
    <?php layout_end(); exit;
}

if ($page === 'room') {
    $id=(int)($_GET['id']??0);
    $stmt=$conn->prepare("SELECT * FROM room_types WHERE id=?"); $stmt->bind_param("i",$id); $stmt->execute(); $room=$stmt->get_result()->fetch_assoc();
    if (!$room) redirect("index.php?page=rooms");
    $stmt=$conn->prepare("SELECT AVG(overall_rating) avg_o, AVG(cleanliness_rating) avg_c, AVG(service_rating) avg_s, COUNT(*) reviews FROM reviews r JOIN bookings b ON b.id=r.booking_id WHERE b.room_type_id=?"); $stmt->bind_param("i",$id); $stmt->execute(); $ratings=$stmt->get_result()->fetch_assoc();
    layout_start("Room Details");
    ?>
    <div class="room-detail"><img src="<?=e($room['image'])?>" alt=""><div class="detail-body"><span class="eyebrow">ROOM TYPE</span><h1><?=e($room['name'])?></h1><div class="price"><?=money($room['price_per_night'])?> <small>/ night</small></div><p><?=e($room['description'])?></p><div class="chips"><span>👥 <?=$room['capacity']?> guests</span><span>⭐ <?=number_format((float)($ratings['avg_o']?:4.6),1)?></span></div><h3>Amenities</h3><div class="amenities"><?=e($room['amenities'])?></div><a class="btn" href="index.php?page=rooms">Check Availability</a></div></div>
    <?php layout_end(); exit;
}

if ($page === 'confirmation') {
    $id=(int)($_GET['id']??0); $gid=guest_id();
    $stmt=$conn->prepare("SELECT b.*,r.name room_name FROM bookings b JOIN room_types r ON r.id=b.room_type_id WHERE b.id=? AND b.guest_id=?"); $stmt->bind_param("ii",$id,$gid); $stmt->execute(); $b=$stmt->get_result()->fetch_assoc();
    layout_start("Booking Confirmation");
    ?>
    <div class="confirmation"><div class="success-icon">✓</div><span class="eyebrow">BOOKING CONFIRMED</span><h1>Your room is reserved.</h1><p>Booking ID <strong><?=e($b['booking_code'])?></strong></p><div class="summary"><div><span>Room</span><b><?=e($b['room_name'])?></b></div><div><span>Dates</span><b><?=e($b['check_in'])?> → <?=e($b['check_out'])?></b></div><div><span>Total</span><b><?=money($b['total_amount'])?></b></div></div><a class="btn" href="index.php?page=bookings">View My Bookings</a></div>
    <?php layout_end(); exit;
}

if ($page === 'bookings') {
    $gid=guest_id(); $stmt=$conn->prepare("SELECT b.*,r.name room_name FROM bookings b JOIN room_types r ON r.id=b.room_type_id WHERE b.guest_id=? ORDER BY b.check_in DESC"); $stmt->bind_param("i",$gid); $stmt->execute(); $rows=$stmt->get_result();
    layout_start("My Bookings"); ?>
    <div class="page-head"><div><span class="eyebrow">RESERVATIONS</span><h1>My Bookings</h1></div><a class="btn" href="index.php?page=rooms">New Booking</a></div>
    <div class="list"><?php while($b=$rows->fetch_assoc()): ?><div class="booking-card"><div><span class="badge <?=e($b['status'])?>"><?=e(ucfirst($b['status']))?></span><h3><?=e($b['room_name'])?></h3><p><?=e($b['check_in'])?> → <?=e($b['check_out'])?> · <?=$b['guests']?> guests</p><small><?=e($b['booking_code'])?></small></div><div class="booking-actions"><strong><?=money($b['total_amount'])?></strong><a class="btn ghost small" href="index.php?page=booking&id=<?=$b['id']?>">Details</a><?php if($b['status']==='confirmed'): ?><form method="post" action="index.php?page=cancel" onsubmit="return confirm('Cancel this booking?')"><input type="hidden" name="booking_id" value="<?=$b['id']?>"><button class="btn danger small">Cancel</button></form><?php endif; ?></div></div><?php endwhile; ?></div>
    <?php layout_end(); exit;
}

if ($page === 'booking') {
    $id=(int)($_GET['id']??0); $gid=guest_id();
    $stmt=$conn->prepare("SELECT b.*,r.name room_name FROM bookings b JOIN room_types r ON r.id=b.room_type_id WHERE b.id=? AND b.guest_id=?"); $stmt->bind_param("ii",$id,$gid); $stmt->execute(); $b=$stmt->get_result()->fetch_assoc();
    layout_start("Booking Details"); ?>
    <div class="detail-card"><span class="eyebrow">BOOKING DETAILS</span><h1><?=e($b['room_name'])?></h1><p class="code"><?=e($b['booking_code'])?></p><div class="summary"><div><span>Check-in</span><b><?=e($b['check_in'])?></b></div><div><span>Check-out</span><b><?=e($b['check_out'])?></b></div><div><span>Guests</span><b><?=e($b['guests'])?></b></div><div><span>Status</span><b><?=e(ucfirst($b['status']))?></b></div><div><span>Total</span><b><?=money($b['total_amount'])?></b></div></div><h3>Cancellation policy</h3><p>You can cancel a confirmed booking before 2 days prior to check-in. Later cancellation is blocked by the system.</p><h3>Modification</h3><p>Need different dates? Submit a request for the receptionist to process.</p><p><a class="btn ghost small" href="../controllers/complete_booking.php?id=<?= $b['id'] ?>">QA: Mark Stay Completed & Earn Points</a></p><form method="post" action="index.php?page=modify" class="form inline-form"><input type="hidden" name="booking_id" value="<?=$b['id']?>"><input type="date" name="new_checkin" required><input type="date" name="new_checkout" required><button class="btn">Request Modification</button></form></div>
    <?php layout_end(); exit;
}

if ($page === 'modify' && $_SERVER['REQUEST_METHOD']==='POST') {
    require_login();
    $stmt=$conn->prepare("INSERT INTO modification_requests(booking_id,guest_id,new_check_in,new_check_out,reason,status) VALUES(?,?,?,?,?,'pending')");
    $reason=trim($_POST['reason']??'Guest requested date change');
   // $stmt->bind_param("iisss",$booking_id= (int)$_POST['booking_id'],$gid=guest_id(),$_POST['new_checkin'],$_POST['new_checkout'],$reason);
   $booking_id = (int)$_POST['booking_id'];
$gid = (int)guest_id();
$new_checkin = $_POST['new_checkin'];
$new_checkout = $_POST['new_checkout'];

$stmt->bind_param(
    "iisss",
    $booking_id,
    $gid,
    $new_checkin,
    $new_checkout,
    $reason
);
    $stmt->execute(); redirect("index.php?page=bookings");
}

if ($page === 'services') {

    require_login();

    $gid = guest_id();

    /*
    |--------------------------------------------------------------------------
    | Get guest bookings for service request
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT
            b.id,
            b.booking_code,
            b.check_in,
            b.check_out,
            b.status,
            r.name
        FROM bookings b
        JOIN room_types r
            ON r.id = b.room_type_id
        WHERE b.guest_id = ?
          AND b.status IN ('confirmed', 'completed')
        ORDER BY b.check_in DESC
    ");

    $stmt->bind_param(
        "i",
        $gid
    );

    $stmt->execute();

    $bookings = $stmt->get_result();

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Get service request history
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT *
        FROM service_requests
        WHERE guest_id = ?
        ORDER BY created_at DESC
    ");

    $stmt->bind_param(
        "i",
        $gid
    );

    $stmt->execute();

    $requests = $stmt->get_result();

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Page UI
    |--------------------------------------------------------------------------
    */

    layout_start("In-Stay Services");
?>

    <div class="page-head">

        <div>

            <span class="eyebrow">
                IN-STAY SERVICES
            </span>

            <h1>
                Request a service
            </h1>

            <p>
                Track every request from pending to completed.
            </p>

        </div>

    </div>


    <form
        method="post"
        action="index.php?page=service"
        class="service-form"
    >

        <label>

            Booking

            <select
                name="booking_id"
                required
            >

                <option value="">
                    Select a booking
                </option>

                <?php while ($b = $bookings->fetch_assoc()): ?>

                    <option value="<?= (int)$b['id'] ?>">

                        <?= e($b['booking_code']) ?>

                        ·

                        <?= e($b['name']) ?>

                        ·

                        <?= e($b['check_in']) ?>

                        to

                        <?= e($b['check_out']) ?>

                    </option>

                <?php endwhile; ?>

            </select>

        </label>


        <label>

            Service

            <select name="service_type">

                <option value="Extra Bed">
                    Extra Bed
                </option>

                <option value="Toiletries">
                    Toiletries
                </option>

                <option value="Laundry">
                    Laundry
                </option>

                <option value="Room Service">
                    Room Service
                </option>

                <option value="Other">
                    Other
                </option>

            </select>

        </label>


        <label>

            Quantity

            <input
                type="number"
                name="quantity"
                min="1"
                value="1"
            >

        </label>


        <label>

            Notes

            <textarea
                name="notes"
                placeholder="Please send it as soon as possible."
            ></textarea>

        </label>


        <button
            type="submit"
            class="btn"
        >
            Submit Request
        </button>

    </form>


    <h2>
        Request History
    </h2>


    <div class="list">

        <?php while ($r = $requests->fetch_assoc()): ?>

            <div class="request-card">

                <b>
                    <?= e($r['service_type']) ?>
                </b>

                <span>
                    x<?= (int)$r['quantity'] ?>
                </span>

                <span
                    class="badge <?= e($r['status']) ?>"
                >
                    <?= e(ucfirst($r['status'])) ?>
                </span>

                <small>
                    <?= e($r['created_at']) ?>
                </small>

                <p>
                    <?= e($r['notes']) ?>
                </p>

            </div>

        <?php endwhile; ?>

    </div>

<?php

    layout_end();

    exit;
}

if ($page === 'reviews') {
    $gid=guest_id();
    $stmt=$conn->prepare("SELECT b.id,b.booking_code,r.name FROM bookings b JOIN room_types r ON r.id=b.room_type_id LEFT JOIN reviews rv ON rv.booking_id=b.id WHERE b.guest_id=? AND b.check_out < CURDATE() AND rv.id IS NULL"); $stmt->bind_param("i",$gid); $stmt->execute(); $eligible=$stmt->get_result();
    $stmt=$conn->prepare("SELECT rv.*,r.name FROM reviews rv JOIN bookings b ON b.id=rv.booking_id JOIN room_types r ON r.id=b.room_type_id WHERE rv.guest_id=? ORDER BY rv.created_at DESC"); $stmt->bind_param("i",$gid); $stmt->execute(); $reviews=$stmt->get_result();
    layout_start("Reviews"); ?>
    <div class="page-head"><div><span class="eyebrow">GUEST REVIEWS</span><h1>Rate your stay</h1></div></div>
    <?php if($eligible->num_rows): ?><form method="post" action="index.php?page=review" class="review-form"><label>Completed Stay<select name="booking_id"><?php while($b=$eligible->fetch_assoc()): ?><option value="<?=$b['id']?>"><?=e($b['booking_code'])?> · <?=e($b['name'])?></option><?php endwhile; ?></select></label><div class="three"><label>Overall<select name="overall"><?php for($i=5;$i>=1;$i--) echo "<option>$i</option>"; ?></select></label><label>Cleanliness<select name="cleanliness"><?php for($i=5;$i>=1;$i--) echo "<option>$i</option>"; ?></select></label><label>Service<select name="service"><?php for($i=5;$i>=1;$i--) echo "<option>$i</option>"; ?></select></label></div><label>Comment<textarea name="comment" required></textarea></label><button class="btn">Submit Review</button></form><?php else: ?><div class="notice">No completed stay is currently waiting for a review.</div><?php endif; ?>
    <div class="list"><?php while($r=$reviews->fetch_assoc()): ?><div class="review-card"><h3><?=e($r['name'])?> <span>★ <?=e($r['overall_rating'])?></span></h3><p><?=e($r['comment'])?></p><small>Cleanliness <?=$r['cleanliness_rating']?> · Service <?=$r['service_rating']?></small></div><?php endwhile; ?></div>
    <?php layout_end(); exit;
}

if ($page === 'loyalty') {

    $gid = guest_id();

    // Calculate total loyalty points
    $stmt = $conn->prepare("
        SELECT COALESCE(SUM(points), 0) AS total_points
        FROM loyalty_transactions
        WHERE guest_id = ?
    ");

    if (!$stmt) {
        die("Loyalty points query error: " . $conn->error);
    }

    $stmt->bind_param("i", $gid);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    $p = (int)($row['total_points'] ?? 0);

    $stmt->close();


    // Loyalty points history
    $stmt = $conn->prepare("
        SELECT *
        FROM loyalty_transactions
        WHERE guest_id = ?
        ORDER BY created_at DESC
    ");

    if (!$stmt) {
        die("Loyalty history query error: " . $conn->error);
    }

    $stmt->bind_param("i", $gid);
    $stmt->execute();

    $tx = $stmt->get_result();


    layout_start("Loyalty Points");
?>

<?php

if ($page === 'billing') {

    $gid = guest_id();

    /*
    |--------------------------------------------------------------------------
    | Get Billing / Invoice History
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT
            i.*,
            b.booking_code,
            r.name AS room_name
        FROM invoices i
        JOIN bookings b
            ON b.id = i.booking_id
        JOIN room_types r
            ON r.id = b.room_type_id
        WHERE b.guest_id = ?
        ORDER BY i.issued_at DESC
    ");

    if (!$stmt) {
        die("Billing query error: " . $conn->error);
    }

    $stmt->bind_param("i", $gid);
    $stmt->execute();

    $rows = $stmt->get_result();

    layout_start("Billing History");
?>

<div class="page-head">
    <div>
        <span class="eyebrow">BILLING</span>
        <h1>Invoices & Receipts</h1>
    </div>
</div>

<?php if ($rows && $rows->num_rows > 0): ?>

    <div class="list">

        <?php while ($invoice = $rows->fetch_assoc()): ?>

            <div class="card">

                <div class="card-head">
                    <div>
                        <h3>
                            <?= e($invoice['booking_code']) ?>
                        </h3>

                        <p>
                            <?= e($invoice['room_name']) ?>
                        </p>
                    </div>

                    <strong>
                        ৳ <?= number_format((float)$invoice['total_amount'], 2) ?>
                    </strong>
                </div>

                <div class="muted">
                    Invoice:
                    <?= e($invoice['invoice_number'] ?? $invoice['id']) ?>
                </div>

                <div class="muted">
                    Issued:
                    <?= e($invoice['issued_at']) ?>
                </div>

                <div style="margin-top:16px;">
                    <a
                        class="btn"
                        href="index.php?page=receipt&id=<?= (int)$invoice['id'] ?>"
                    >
                        View Receipt
                    </a>
                </div>

            </div>

        <?php endwhile; ?>

    </div>

<?php else: ?>

    <div class="notice">
        No invoices or billing records found.
    </div>

<?php endif; ?>

<?php
    $stmt->close();

    layout_end();
    exit;
}
?>

if ($page === 'profile') {
    $gid=guest_id(); $stmt=$conn->prepare("SELECT * FROM guests WHERE id=?"); $stmt->bind_param("i",$gid); $stmt->execute(); $u=$stmt->get_result()->fetch_assoc();
    layout_start("Profile"); ?><div class="detail-card"><span class="eyebrow">MY PROFILE</span><?php if(!empty($u['profile_picture'])): ?><img class="avatar" src="<?=e($u['profile_picture'])?>" alt="Profile"><?php endif; ?><h1><?=e($u['full_name'])?></h1><form method="post" action="../controllers/profile_save.php" enctype="multipart/form-data" class="form"><label>Profile Picture<input type="file" name="profile_picture" accept="image/*"></label><label>Full Name<input name="full_name" value="<?=e($u['full_name'])?>" required></label><label>Email<input value="<?=e($u['email'])?>" disabled></label><label>Phone<input name="phone" value="<?=e($u['phone'])?>"></label><label>Nationality<input name="nationality" value="<?=e($u['nationality'])?>"></label><label>ID Number<input name="id_number" value="<?=e($u['id_number'])?>"></label><button class="btn">Save Profile</button></form><hr><h3>Change Password</h3><form method="post" action="../controllers/password_save.php" class="form"><label>Current Password<input type="password" name="current" required></label><label>New Password<input type="password" name="new" required></label><button class="btn">Update Password</button></form></div><?php layout_end(); exit;
}
?>
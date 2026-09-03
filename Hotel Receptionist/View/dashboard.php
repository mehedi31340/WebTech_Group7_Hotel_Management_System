<?php

require_once "../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {

    header("Location: login.php");
    exit();
}

$today = date("Y-m-d");

function oneValue($conn, $sql, $types = "", $params = []) {

    $s = mysqli_prepare($conn, $sql);

    if ($types) {
        mysqli_stmt_bind_param($s, $types, ...$params);
    }

    mysqli_stmt_execute($s);

    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($s));

    return $r[array_key_first($r)];
}

function findBookingById($conn, $id, $requireCheckedIn = false) {

    $sql = "SELECT b.*, r.room_no, r.room_type,
                   (SELECT base_amount FROM billing WHERE booking_id = b.id) AS base_amount,
                   (SELECT extras_amount FROM billing WHERE booking_id = b.id) AS extras_amount,
                   (SELECT discount_amount FROM billing WHERE booking_id = b.id) AS discount_amount,
                   (SELECT total_amount FROM billing WHERE booking_id = b.id) AS bill_total,
                   (SELECT payment_status FROM billing WHERE booking_id = b.id) AS payment_status
            FROM bookings b
            JOIN rooms r ON b.room_id = r.id
            WHERE b.id = ?";

    if ($requireCheckedIn) {
        $sql .= " AND b.status = 'Checked In'";
    }

    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);

    return mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
}

function fmtDate($date) {
    if (!$date) {
        return "";
    }
    return date("d-m-Y", strtotime($date));
}

$checkins = oneValue(
    $conn,
    "SELECT COUNT(*) c FROM bookings WHERE checkin=? AND status='Booked'",
    "s",
    [$today]
);

$checkouts = oneValue(
    $conn,
    "SELECT COUNT(*) c FROM bookings WHERE checkout=? AND status='Checked In'",
    "s",
    [$today]
);

$checkedin = oneValue(
    $conn,
    "SELECT COUNT(*) c FROM bookings WHERE status='Checked In'"
);

$available = oneValue(
    $conn,
    "SELECT COUNT(*) c FROM rooms WHERE status='Available'"
);

$searchBookingId = trim($_GET["booking_id"] ?? "");


$billingCols = "(SELECT total_amount FROM billing WHERE booking_id = b.id) AS bill_total,
                 (SELECT payment_status FROM billing WHERE booking_id = b.id) AS payment_status";

$allBookings = mysqli_query(
    $conn,
    "SELECT b.*, r.room_no, r.room_type, $billingCols
     FROM bookings b
     JOIN rooms r ON b.room_id = r.id
     ORDER BY b.id DESC"
);

if ($searchBookingId !== "" && ctype_digit($searchBookingId)) {

    $stmt = mysqli_prepare(
        $conn,
        "SELECT b.*, r.room_no, r.room_type, $billingCols
         FROM bookings b
         JOIN rooms r ON b.room_id = r.id
         WHERE b.id = ?
         ORDER BY b.id DESC"
    );

    $searchId = (int)$searchBookingId;

    mysqli_stmt_bind_param($stmt, "i", $searchId);
    mysqli_stmt_execute($stmt);

    $bookings = mysqli_stmt_get_result($stmt);

} elseif ($searchBookingId !== "") {

    $stmt = mysqli_prepare(
        $conn,
        "SELECT b.*, r.room_no, r.room_type, $billingCols
         FROM bookings b
         JOIN rooms r ON b.room_id = r.id
         WHERE 1 = 0"
    );
    mysqli_stmt_execute($stmt);
    $bookings = mysqli_stmt_get_result($stmt);

} else {

    $bookings = $allBookings;
}

$services = mysqli_query(
    $conn,
    "SELECT s.*, r.room_no
     FROM service_requests s
     JOIN rooms r ON s.room_id = r.id
     WHERE s.status <> 'completed'
     ORDER BY s.id DESC"
);

$requests = mysqli_query(
    $conn,
    "SELECT e.*, b.guest_name
     FROM early_late_requests e
     JOIN bookings b ON e.booking_id = b.id
     WHERE e.status = 'pending'
     ORDER BY e.id DESC"
);

$paymentSearchId = trim($_GET["payment_id"] ?? "");
$paymentBooking = null;
if ($paymentSearchId !== "" && ctype_digit($paymentSearchId)) {
    $paymentBooking = findBookingById($conn, (int)$paymentSearchId);
}

$serviceSearchId = trim($_GET["service_id"] ?? "");
$serviceBooking = null;
if ($serviceSearchId !== "" && ctype_digit($serviceSearchId)) {
    $serviceBooking = findBookingById($conn, (int)$serviceSearchId, true);
}

$earlyLateSearchId = trim($_GET["earlylate_id"] ?? "");
$earlyLateBooking = null;
if ($earlyLateSearchId !== "" && ctype_digit($earlyLateSearchId)) {
    $earlyLateBooking = findBookingById($conn, (int)$earlyLateSearchId, true);
}

$bookingMsg = $_SESSION["bookingMessage"] ?? "";
$bookingErr = $_SESSION["bookingError"] ?? "";

$paymentMsg = $_SESSION["paymentMessage"] ?? "";
$paymentErr = $_SESSION["paymentError"] ?? "";

$serviceMsg = $_SESSION["serviceMessage"] ?? "";
$serviceErr = $_SESSION["serviceError"] ?? "";

$earlyLateMsg = $_SESSION["earlyLateMessage"] ?? "";
$earlyLateErr = $_SESSION["earlyLateError"] ?? "";

unset(
    $_SESSION["bookingMessage"],
    $_SESSION["bookingError"],
    $_SESSION["paymentMessage"],
    $_SESSION["paymentError"],
    $_SESSION["serviceMessage"],
    $_SESSION["serviceError"],
    $_SESSION["earlyLateMessage"],
    $_SESSION["earlyLateError"]
);

?>

<!DOCTYPE html>
<html>

<head>

    <title>Hotel Receptionist Dashboard</title>

    <link rel="stylesheet" href="style.css">

    <script>

        function loadRooms() {

            var x = new XMLHttpRequest();

            x.open("GET", "../Controller/api/rooms.php", true);

            x.onload = function () {

                if (x.status === 200) {

                    var d = JSON.parse(x.responseText);
                    var h = "";

                    d.forEach(function (r) {

                        h += "<tr>";
                        h += "<td>" + r.room_no + "</td>";
                        h += "<td>" + r.room_type + "</td>";
                        h += "<td>" + r.price + " BDT</td>";
                        h += "<td>" + r.status + "</td>";
                        h += "</tr>";
                    });

                    document.getElementById("roomRows").innerHTML = h;
                }
            };

            x.send();
        }

        setInterval(loadRooms, 5000);

        window.onload = loadRooms;

    </script>

</head>

<body>

<div class="layout">

    <aside class="sidebar">

        <h2>Hotel Reception</h2>

        <p class="sidebar-user">
            Welcome,<br>
            <b><?php echo htmlspecialchars($_SESSION["name"]); ?></b>
        </p>

        <nav>

            <a class="active" href="dashboard.php">
                Dashboard
            </a>

            <a href="receptionist/booking.php">
                New Booking
            </a>

            <a href="receptionist/rooms.php">
                Room Status
            </a>

            <a href="#bookings">
                Bookings
            </a>

            <a href="#payment">
                Payment
            </a>

            <a href="#services">
                Service Requests
            </a>

            <a href="#early-late">
                Early / Late Requests
            </a>

            <a href="#room-board">
                Room Status Board
            </a>

            <a href="report.php">
                Daily Report
            </a>

            <a class="logout" href="../Controller/logout.php">
                Logout
            </a>

        </nav>

    </aside>

    <main class="main-content">

        <div class="card">

            <h1>Hotel Receptionist Dashboard</h1>

            <div class="stats">

                <div>
                    <b><?php echo $checkins; ?></b>
                    <br>
                    Today's Check-ins
                </div>

                <div>
                    <b><?php echo $checkouts; ?></b>
                    <br>
                    Today's Check-outs
                </div>

                <div>
                    <b><?php echo $checkedin; ?></b>
                    <br>
                    Checked In
                </div>

                <div>
                    <b><?php echo $available; ?></b>
                    <br>
                    Available Rooms
                </div>

            </div>

        </div>

        <div class="card" id="bookings">

            <h2>Bookings</h2>

            <p>
                Search for a booking using its Booking ID.
            </p>

            <?php if ($bookingMsg) { ?>

                <p class="success">
                    <?php echo htmlspecialchars($bookingMsg); ?>
                </p>

            <?php } ?>

            <?php if ($bookingErr) { ?>

                <p class="error">
                    <?php echo htmlspecialchars($bookingErr); ?>
                </p>

            <?php } ?>

            <form method="get" class="search-form">

                <label>
                    Search by Booking ID
                </label>

                <input
                    type="number"
                    name="booking_id"
                    min="1"
                    value="<?php echo htmlspecialchars($searchBookingId); ?>"
                    placeholder="Enter Booking ID"
                >

                <button type="submit">
                    Search
                </button>

                <a class="show-all" href="dashboard.php">
                    Show All
                </a>

            </form>

            <?php

            if (
                $searchBookingId !== ""
                && (!ctype_digit($searchBookingId) || mysqli_num_rows($bookings) === 0)
            ) {

            ?>

                <p class="error">
                    No booking found with ID #<?php echo htmlspecialchars($searchBookingId); ?>.
                </p>

            <?php } ?>

            <table>

                <tr>
                    <th>ID</th>
                    <th>Guest</th>
                    <th>Phone</th>
                    <th>Room</th>
                    <th>Check In</th>
                    <th>Check Out</th>
                    <th>Total</th>
                    <th>Payment</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php while ($b = mysqli_fetch_assoc($bookings)) {

                    $billTotal = $b["bill_total"] !== null ? $b["bill_total"] : $b["total"];
                    $isPaid = $b["payment_status"] === "paid";

                ?>

                    <tr>

                        <td><?php echo $b["id"]; ?></td>

                        <td>
                            <?php echo htmlspecialchars($b["guest_name"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($b["phone"]); ?>
                        </td>

                        <td>
                            <?php echo htmlspecialchars($b["room_no"]); ?>
                            (<?php echo htmlspecialchars($b["room_type"]); ?>)
                        </td>

                        <td><?php echo fmtDate($b["checkin"]); ?></td>
                        <td><?php echo fmtDate($b["checkout"]); ?></td>
                        <td><?php echo $billTotal; ?> BDT</td>

                        <td>

                            <?php if ($isPaid) { ?>
                                <span class="success">Paid</span>
                            <?php } else { ?>
                                <span class="error">Payment Required</span>
                            <?php } ?>

                        </td>

                        <td><?php echo $b["status"]; ?></td>

                        <td>

                            <?php if ($b["status"] == "Booked") { ?>

                                <form action="../Controller/checkin.php" method="post" class="inline-form">

                                    <input type="hidden" name="booking_id" value="<?php echo $b["id"]; ?>">

                                    <select name="payment_method" required>
                                        <option value="">Payment...</option>
                                        <option value="Cash">Cash</option>
                                        <option value="Card">Card</option>
                                        <option value="Mobile Banking">Mobile Banking</option>
                                    </select>

                                    <button type="submit">Check In</button>

                                </form>

                            <?php } elseif ($b["status"] == "Checked In") { ?>

                                <?php if ($isPaid) { ?>

                                    <a href="../Controller/checkout.php?id=<?php echo $b["id"]; ?>">
                                        Check Out
                                    </a>

                                <?php } else { ?>

                                    <span
                                        class="error"
                                        title="Payment must be completed before this guest can check out."
                                    >
                                        Payment Required
                                    </span>

                                <?php } ?>

                            <?php } else { ?>

                                Done

                            <?php } ?>

                            |

                            <a
                                class="delete-link"
                                href="../Controller/deleteBooking.php?id=<?php echo $b["id"]; ?>"
                                onclick="return confirm('Delete booking #<?php echo $b["id"]; ?> for <?php echo htmlspecialchars(addslashes($b["guest_name"])); ?>? This cannot be undone and the room will become Available.');"
                            >
                                Delete
                            </a>

                        </td>

                    </tr>

                <?php } ?>

            </table>

        </div>

        <div class="card" id="payment">

            <h2>Payment</h2>

            <?php if ($paymentMsg) { ?>

                <p class="success">
                    <?php echo htmlspecialchars($paymentMsg); ?>
                </p>

            <?php } ?>

            <?php if ($paymentErr) { ?>

                <p class="error">
                    <?php echo htmlspecialchars($paymentErr); ?>
                </p>

            <?php } ?>

            <form method="get" class="search-form" action="dashboard.php#payment">

                <label>
                    Find by Booking ID
                </label>

                <input
                    type="number"
                    name="payment_id"
                    min="1"
                    value="<?php echo htmlspecialchars($paymentSearchId); ?>"
                    placeholder="Enter Booking ID"
                >

                <button type="submit">
                    Search
                </button>

            </form>

            <?php if ($paymentSearchId !== "" && !$paymentBooking) { ?>

                <p class="error">
                    No booking found with ID #<?php echo htmlspecialchars($paymentSearchId); ?>.
                </p>

            <?php } ?>

            <?php if ($paymentBooking) { ?>

                <div class="found-info">

                    <p>
                        <b>Booking #<?php echo $paymentBooking["id"]; ?></b> -
                        <?php echo htmlspecialchars($paymentBooking["guest_name"]); ?>
                        (<?php echo htmlspecialchars($paymentBooking["phone"]); ?>)
                        <br>
                        Room <?php echo htmlspecialchars($paymentBooking["room_no"]); ?>
                        (<?php echo htmlspecialchars($paymentBooking["room_type"]); ?>) -
                        Status: <?php echo htmlspecialchars($paymentBooking["status"]); ?>
                        <br>
                        Room charge: <?php echo $paymentBooking["base_amount"] ?? $paymentBooking["total"]; ?> BDT -
                        Service extras: <?php echo $paymentBooking["extras_amount"] ?? 0; ?> BDT -
                        Discount: <?php echo $paymentBooking["discount_amount"] ?? 0; ?> BDT
                        <br>
                        <b>Total due: <?php echo $paymentBooking["bill_total"] ?? $paymentBooking["total"]; ?> BDT</b> -

                        <?php if ($paymentBooking["payment_status"] === "paid") { ?>
                            <span class="success">Paid</span>
                        <?php } else { ?>
                            <span class="error">Payment Required</span>
                        <?php } ?>

                    </p>

                </div>

                <?php if ($paymentBooking["payment_status"] !== "paid") { ?>

                    <form action="../Controller/payment.php" method="post">

                        <input
                            type="hidden"
                            name="booking_id"
                            value="<?php echo $paymentBooking["id"]; ?>"
                        >

                        <select name="payment_method">

                            <option>Cash</option>
                            <option>Card</option>
                            <option>Mobile Banking</option>

                        </select>

                        <button type="submit">
                            Mark Paid
                        </button>

                    </form>

                <?php } ?>

            <?php } ?>

        </div>

        <div class="card" id="services">

            <h2>Service Requests</h2>

            <?php if ($serviceMsg) { ?>

                <p class="success">
                    <?php echo htmlspecialchars($serviceMsg); ?>
                </p>

            <?php } ?>

            <?php if ($serviceErr) { ?>

                <p class="error">
                    <?php echo htmlspecialchars($serviceErr); ?>
                </p>

            <?php } ?>

            <form method="get" class="search-form" action="dashboard.php#services">

                <label>
                    Find by Booking ID
                </label>

                <input
                    type="number"
                    name="service_id"
                    min="1"
                    value="<?php echo htmlspecialchars($serviceSearchId); ?>"
                    placeholder="Enter Booking ID"
                >

                <button type="submit">
                    Search
                </button>

            </form>

            <?php if ($serviceSearchId !== "" && !$serviceBooking) { ?>

                <p class="error">
                    No checked-in booking found with ID #<?php echo htmlspecialchars($serviceSearchId); ?>.
                </p>

            <?php } ?>

            <?php if ($serviceBooking) { ?>

                <div class="found-info">

                    <p>
                        <b>Booking #<?php echo $serviceBooking["id"]; ?></b> -
                        <?php echo htmlspecialchars($serviceBooking["guest_name"]); ?>
                        - Room <?php echo htmlspecialchars($serviceBooking["room_no"]); ?>
                        (<?php echo htmlspecialchars($serviceBooking["room_type"]); ?>) -
                        Status: <?php echo htmlspecialchars($serviceBooking["status"]); ?> -
                        Current bill: <?php echo $serviceBooking["bill_total"] ?? $serviceBooking["total"]; ?> BDT
                    </p>

                </div>

                <form action="../Controller/service.php" method="post">

                    <input
                        type="hidden"
                        name="booking_id"
                        value="<?php echo $serviceBooking["id"]; ?>"
                    >

                    <select name="service_type">
                        <option>extra_bed</option>
                        <option>toiletries</option>
                        <option>laundry</option>
                        <option>room_service</option>
                        <option>other</option>
                    </select>

                    <input
                        name="description"
                        placeholder="Description"
                    >

                    <input
                        type="number"
                        name="amount"
                        min="0"
                        step="0.01"
                        placeholder="Amount BDT (leave blank for default)"
                    >

                    <button type="submit">
                        Add Request
                    </button>

                </form>

            <?php } ?>

            <table>

                <tr>
                    <th>Room</th>
                    <th>Service</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                <?php while ($s = mysqli_fetch_assoc($services)) { ?>

                    <tr>

                        <td><?php echo htmlspecialchars($s["room_no"]); ?></td>
                        <td><?php echo htmlspecialchars($s["service_type"]); ?></td>
                        <td><?php echo htmlspecialchars($s["description"] ?? ""); ?></td>
                        <td><?php echo $s["price"]; ?> BDT</td>
                        <td><?php echo htmlspecialchars($s["status"]); ?></td>

                        <td>

                            <a href="../Controller/serviceStatus.php?id=<?php echo $s["id"]; ?>&status=in_progress">
                                In Progress
                            </a>

                            |

                            <a href="../Controller/serviceStatus.php?id=<?php echo $s["id"]; ?>&status=completed">
                                Completed
                            </a>

                        </td>

                    </tr>

                <?php } ?>

            </table>

        </div>

        <div class="card" id="early-late">

            <h2>Early Check-in / Late Checkout</h2>

            <?php if ($earlyLateMsg) { ?>

                <p class="success">
                    <?php echo htmlspecialchars($earlyLateMsg); ?>
                </p>

            <?php } ?>

            <?php if ($earlyLateErr) { ?>

                <p class="error">
                    <?php echo htmlspecialchars($earlyLateErr); ?>
                </p>

            <?php } ?>

            <form method="get" class="search-form" action="dashboard.php#early-late">

                <label>
                    Find by Booking ID
                </label>

                <input
                    type="number"
                    name="earlylate_id"
                    min="1"
                    value="<?php echo htmlspecialchars($earlyLateSearchId); ?>"
                    placeholder="Enter Booking ID"
                >

                <button type="submit">
                    Search
                </button>

            </form>

            <?php if ($earlyLateSearchId !== "" && !$earlyLateBooking) { ?>

                <p class="error">
                    No checked-in booking found with ID #<?php echo htmlspecialchars($earlyLateSearchId); ?>.
                </p>

            <?php } ?>

            <?php if ($earlyLateBooking) { ?>

                <div class="found-info">

                    <p>
                        <b>Booking #<?php echo $earlyLateBooking["id"]; ?></b> -
                        <?php echo htmlspecialchars($earlyLateBooking["guest_name"]); ?>
                        - Room <?php echo htmlspecialchars($earlyLateBooking["room_no"]); ?>
                        (<?php echo htmlspecialchars($earlyLateBooking["room_type"]); ?>) -
                        Status: <?php echo htmlspecialchars($earlyLateBooking["status"]); ?>
                    </p>

                </div>

                <form action="../Controller/earlyLate.php" method="post">

                    <input
                        type="hidden"
                        name="booking_id"
                        value="<?php echo $earlyLateBooking["id"]; ?>"
                    >

                    <select name="request_type">

                        <option value="early_checkin">
                            Early Check-in
                        </option>

                        <option value="late_checkout">
                            Late Checkout
                        </option>

                    </select>

                    <input
                        type="datetime-local"
                        name="requested_time"
                        required
                    >

                    <button type="submit">
                        Request
                    </button>

                </form>

            <?php } ?>

            <?php while ($e = mysqli_fetch_assoc($requests)) { ?>

                <p>
                    Booking #<?php echo $e["booking_id"]; ?> -
                    <?php echo htmlspecialchars($e["guest_name"]); ?> -
                    <?php echo htmlspecialchars($e["request_type"]); ?>

                    <a href="../Controller/earlyLateStatus.php?id=<?php echo $e["id"]; ?>&status=approved">
                        Approve
                    </a>

                    |

                    <a href="../Controller/earlyLateStatus.php?id=<?php echo $e["id"]; ?>&status=declined">
                        Decline
                    </a>
                </p>

            <?php } ?>

        </div>

        <div class="card" id="room-board">

            <h2>Room Status Board (AJAX)</h2>

            <table>

                <tr>
                    <th>Room</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>

                <tbody id="roomRows">
                </tbody>

            </table>

            <p>
                Room status refreshes automatically every 5 seconds.
            </p>

        </div>

    </main>

</div>

</body>

</html>

<?php
require_once "../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: login.php");
    exit();
}

$today = date("Y-m-d");

function reportValue($conn, $sql, $types = "", $params = []) {

    $s = mysqli_prepare($conn, $sql);

    if ($types) {
        mysqli_stmt_bind_param($s, $types, ...$params);
    }

    mysqli_stmt_execute($s);

    $r = mysqli_fetch_assoc(mysqli_stmt_get_result($s));

    return $r[array_key_first($r)];
}

// Display-only date formatter, see View/dashboard.php for details.
function fmtDate($date) {
    if (!$date) {
        return "";
    }
    return date("d-m-Y", strtotime($date));
}

$arrivals = reportValue(
    $conn,
    "SELECT COUNT(*) c FROM bookings WHERE checkin = ?",
    "s",
    [$today]
);

$departures = reportValue(
    $conn,
    "SELECT COUNT(*) c FROM bookings WHERE checkout = ?",
    "s",
    [$today]
);

$walkIns = reportValue(
    $conn,
    "SELECT COUNT(*) c FROM bookings WHERE source = 'walk_in' AND DATE(created_at) = ?",
    "s",
    [$today]
);

$revenue = reportValue(
    $conn,
    "SELECT COALESCE(SUM(total_amount), 0) total FROM billing WHERE payment_status = 'paid' AND DATE(paid_at) = ?",
    "s",
    [$today]
);

$occupied = reportValue($conn, "SELECT COUNT(*) c FROM rooms WHERE status = 'Occupied'");
$available = reportValue($conn, "SELECT COUNT(*) c FROM rooms WHERE status = 'Available'");
?>

<!DOCTYPE html>
<html>

<head>

    <title>Daily Operations Report</title>

    <link rel="stylesheet" href="style.css">

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

            <a href="dashboard.php">
                Dashboard
            </a>

            <a href="receptionist/booking.php">
                New Booking
            </a>

            <a href="receptionist/rooms.php">
                Room Status
            </a>

            <a href="dashboard.php#bookings">
                Bookings
            </a>

            <a href="dashboard.php#payment">
                Payment
            </a>

            <a href="dashboard.php#services">
                Service Requests
            </a>

            <a href="dashboard.php#early-late">
                Early / Late Requests
            </a>

            <a class="active" href="report.php">
                Daily Report
            </a>

            <a class="logout" href="../Controller/logout.php">
                Logout
            </a>

        </nav>

    </aside>

    <main class="main-content">

        <div class="card">

            <h1>Daily Operations Report</h1>

            <p>Date: <?php echo fmtDate($today); ?></p>

            <table>

                <tr>
                    <th>Item</th>
                    <th>Total</th>
                </tr>

                <tr>
                    <td>Arrivals</td>
                    <td><?php echo $arrivals; ?></td>
                </tr>

                <tr>
                    <td>Departures</td>
                    <td><?php echo $departures; ?></td>
                </tr>

                <tr>
                    <td>Walk-in Bookings</td>
                    <td><?php echo $walkIns; ?></td>
                </tr>

                <tr>
                    <td>Revenue Collected</td>
                    <td><?php echo $revenue; ?> BDT</td>
                </tr>

                <tr>
                    <td>Occupied Rooms</td>
                    <td><?php echo $occupied; ?></td>
                </tr>

                <tr>
                    <td>Available Rooms</td>
                    <td><?php echo $available; ?></td>
                </tr>

            </table>

            <div class="form-actions">
                <button onclick="window.print()">Print Report</button>
                <a class="btn-secondary" href="dashboard.php">Back to Dashboard</a>
            </div>

        </div>

    </main>

</div>

</body>

</html>

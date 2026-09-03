<?php
require_once "../../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: ../login.php");
    exit();
}

$error = $_SESSION["bookingError"] ?? "";
unset($_SESSION["bookingError"]);

$rooms = mysqli_query(
    $conn,
    "SELECT * FROM rooms WHERE status = 'Available' ORDER BY room_no"
);
?>

<!DOCTYPE html>
<html>

<head>

    <title>New Booking</title>

    <link rel="stylesheet" href="../style.css">

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

            <a href="../dashboard.php">
                Dashboard
            </a>

            <a class="active" href="booking.php">
                New Booking
            </a>

            <a href="rooms.php">
                Room Status
            </a>

            <a href="../dashboard.php#bookings">
                Bookings
            </a>

            <a href="../dashboard.php#payment">
                Payment
            </a>

            <a href="../dashboard.php#services">
                Service Requests
            </a>

            <a href="../dashboard.php#early-late">
                Early / Late Requests
            </a>

            <a href="../report.php">
                Daily Report
            </a>

            <a class="logout" href="../../Controller/logout.php">
                Logout
            </a>

        </nav>

    </aside>

    <main class="main-content">

        <div class="card booking-form-card">

            <h1>New Booking</h1>

            <?php if ($error) { ?>

                <p class="error">
                    <?php echo htmlspecialchars($error); ?>
                </p>

            <?php } ?>

            <form action="../../Controller/addBooking.php" method="post" class="booking-form">

                <fieldset>

                    <legend>Guest Details</legend>

                    <table class="form-table">

                        <tr>
                            <td><label for="guest_name">Guest Name</label></td>
                            <td>
                                <input
                                    type="text"
                                    id="guest_name"
                                    name="guest_name"
                                    placeholder="Full name"
                                    required
                                >
                            </td>
                        </tr>

                        <tr>
                            <td><label for="phone">Phone</label></td>
                            <td>
                                <input
                                    type="text"
                                    id="phone"
                                    name="phone"
                                    placeholder="e.g. 01XXXXXXXXX"
                                    required
                                >
                            </td>
                        </tr>

                    </table>

                </fieldset>

                <fieldset>

                    <legend>Stay Details</legend>

                    <table class="form-table">

                        <tr>
                            <td><label for="room_id">Room</label></td>
                            <td>

                                <select id="room_id" name="room_id" required>

                                    <option value="">Select Room</option>

                                    <?php while ($r = mysqli_fetch_assoc($rooms)) { ?>

                                        <option value="<?php echo $r["id"]; ?>">
                                            Room <?php echo htmlspecialchars($r["room_no"]); ?>
                                            &mdash; <?php echo htmlspecialchars($r["room_type"]); ?>
                                            &mdash; <?php echo $r["price"]; ?> BDT / night
                                        </option>

                                    <?php } ?>

                                </select>

                            </td>
                        </tr>

                        <tr>
                            <td><label for="checkin">Check In</label></td>
                            <td>
                                <input
                                    type="date"
                                    id="checkin"
                                    name="checkin"
                                    value="<?php echo date('Y-m-d'); ?>"
                                    min="<?php echo date('Y-m-d'); ?>"
                                    required
                                >
                            </td>
                        </tr>

                        <tr>
                            <td><label for="checkout">Check Out</label></td>
                            <td>
                                <input
                                    type="date"
                                    id="checkout"
                                    name="checkout"
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                    required
                                >
                            </td>
                        </tr>

                        <tr>
                            <td><label for="guests">Number of Guests</label></td>
                            <td>
                                <input
                                    type="number"
                                    id="guests"
                                    name="guests"
                                    value="1"
                                    min="1"
                                    required
                                >
                            </td>
                        </tr>

                        <tr>
                            <td><label for="special_request">Special Request</label></td>
                            <td>
                                <input
                                    type="text"
                                    id="special_request"
                                    name="special_request"
                                    placeholder="Optional"
                                >
                            </td>
                        </tr>

                        <tr>
                            <td><label for="payment_method">Payment Method</label></td>
                            <td>

                                <select id="payment_method" name="payment_method">
                                    <option value="">Select (required for Book &amp; Check In)</option>
                                    <option value="Cash">Cash</option>
                                    <option value="Card">Card</option>
                                    <option value="Mobile Banking">Mobile Banking</option>
                                </select>

                            </td>
                        </tr>

                    </table>

                </fieldset>

                <div class="form-actions">
                    <button type="submit" name="action" value="checkin">Book &amp; Check In</button>
                    <button type="submit" name="action" value="reserve">Book Only</button>
                    <a class="btn-secondary" href="../dashboard.php">Cancel</a>
                </div>

            </form>

        </div>

    </main>

</div>

</body>

</html>

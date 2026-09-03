<?php
require_once "../../config/database.php";

if (!isset($_SESSION["isLoggedIn"]) || $_SESSION["role"] !== "receptionist") {
    header("Location: ../login.php");
    exit();
}

$rooms = mysqli_query($conn, "SELECT * FROM rooms ORDER BY room_no");
?>

<!DOCTYPE html>
<html>

<head>

    <title>Room Status</title>

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

            <a href="booking.php">
                New Booking
            </a>

            <a class="active" href="rooms.php">
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

        <div class="card">

            <h1>Room Status Board</h1>

            <p>
                Live update every 5 seconds using AJAX.
            </p>

            <table>

                <tr>
                    <th>Room</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Status</th>
                </tr>

                <tbody id="rows">

                    <?php while ($r = mysqli_fetch_assoc($rooms)) { ?>

                        <tr>
                            <td><?php echo htmlspecialchars($r["room_no"]); ?></td>
                            <td><?php echo htmlspecialchars($r["room_type"]); ?></td>
                            <td><?php echo $r["price"]; ?> BDT</td>
                            <td><?php echo htmlspecialchars($r["status"]); ?></td>
                        </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </main>

</div>

<script>

    function updateRooms() {

        var x = new XMLHttpRequest();

        x.open("GET", "../../Controller/api/rooms.php");

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

                document.getElementById("rows").innerHTML = h;
            }
        };

        x.send();
    }

    setInterval(updateRooms, 5000);

</script>

</body>

</html>

<?php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
$conn = mysqli_connect("localhost", "root", "", "hotel_simple");
if (!$conn) { die("Database connection failed: " . mysqli_connect_error()); }
mysqli_set_charset($conn, "utf8mb4");
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Makes sure a booking has a billing row, creating a pending one from its
// room total if it doesn't already have one (e.g. an older booking from
// before billing existed, or one created outside addBooking.php).
//
// This is a single atomic INSERT ... ON DUPLICATE KEY UPDATE (an "upsert")
// rather than a SELECT to check followed by a separate INSERT. A
// check-then-insert has a race: two requests can both see "no row yet" and
// both try to insert, which used to be able to leave a booking with more
// than one billing row (and the bookings table then showing that booking
// twice, once per billing row it joined against). Relying on the
// booking_id UNIQUE constraint plus a single upsert statement makes that
// impossible — there can only ever be one billing row per booking.
function ensureBilling($conn, $bookingId) {

    $b = mysqli_prepare($conn, "SELECT total FROM bookings WHERE id = ?");
    mysqli_stmt_bind_param($b, "i", $bookingId);
    mysqli_stmt_execute($b);
    $booking = mysqli_fetch_assoc(mysqli_stmt_get_result($b));
    $base = $booking ? (float)$booking["total"] : 0;

    $init = mysqli_prepare(
        $conn,
        "INSERT INTO billing (booking_id, base_amount, extras_amount, discount_amount, total_amount, payment_status)
         VALUES (?, ?, 0, 0, ?, 'pending')
         ON DUPLICATE KEY UPDATE booking_id = booking_id"
    );
    mysqli_stmt_bind_param($init, "idd", $bookingId, $base, $base);
    mysqli_stmt_execute($init);
}

// Sets the room-charge portion of a bill (e.g. after modifying booking
// dates changes the total) and recalculates the bill around it.
function setBillingBase($conn, $bookingId, $baseAmount) {

    ensureBilling($conn, $bookingId);

    $u = mysqli_prepare($conn, "UPDATE billing SET base_amount = ? WHERE booking_id = ?");
    mysqli_stmt_bind_param($u, "di", $baseAmount, $bookingId);
    mysqli_stmt_execute($u);

    recalcBilling($conn, $bookingId);
}

// Recomputes extras_amount (sum of all service request charges for this
// booking) and total_amount (base + extras - discount). If the bill was
// already marked 'paid' but new charges push the total above what was
// paid, it's dropped back to 'pending' since there's now a balance due.
function recalcBilling($conn, $bookingId) {

    ensureBilling($conn, $bookingId);

    $bq = mysqli_prepare($conn, "SELECT base_amount, discount_amount, total_amount, payment_status FROM billing WHERE booking_id = ?");
    mysqli_stmt_bind_param($bq, "i", $bookingId);
    mysqli_stmt_execute($bq);
    $bill = mysqli_fetch_assoc(mysqli_stmt_get_result($bq));

    if (!$bill) {
        return;
    }

    $eq = mysqli_prepare($conn, "SELECT COALESCE(SUM(price), 0) extras FROM service_requests WHERE booking_id = ?");
    mysqli_stmt_bind_param($eq, "i", $bookingId);
    mysqli_stmt_execute($eq);
    $extras = (float)mysqli_fetch_assoc(mysqli_stmt_get_result($eq))["extras"];

    $newTotal = (float)$bill["base_amount"] + $extras - (float)$bill["discount_amount"];

    $newStatus = $bill["payment_status"];
    if ($newStatus === "paid" && $newTotal > (float)$bill["total_amount"] + 0.01) {
        $newStatus = "pending";
    }

    $u = mysqli_prepare($conn, "UPDATE billing SET extras_amount = ?, total_amount = ?, payment_status = ? WHERE booking_id = ?");
    mysqli_stmt_bind_param($u, "ddsi", $extras, $newTotal, $newStatus, $bookingId);
    mysqli_stmt_execute($u);
}

// Guests must pay at check-in — both for an instant walk-in (book & check
// in immediately) and for checking in a guest who reserved earlier. This
// brings the bill up to date and marks it paid with the chosen method in
// one place, so both flows charge the guest the same way. If a service is
// added after this, recalcBilling() above will flip the bill back to
// 'pending' ("Payment Required") on its own once the new total exceeds
// what was already paid.
function markBillingPaid($conn, $bookingId, $method) {

    recalcBilling($conn, $bookingId);

    $u = mysqli_prepare(
        $conn,
        "UPDATE billing SET payment_method = ?, payment_status = 'paid', paid_at = NOW() WHERE booking_id = ?"
    );
    mysqli_stmt_bind_param($u, "si", $method, $bookingId);
    mysqli_stmt_execute($u);

    $q = mysqli_prepare($conn, "SELECT total_amount FROM billing WHERE booking_id = ?");
    mysqli_stmt_bind_param($q, "i", $bookingId);
    mysqli_stmt_execute($q);
    $row = mysqli_fetch_assoc(mysqli_stmt_get_result($q));

    return $row ? (float)$row["total_amount"] : 0.0;
}
?>

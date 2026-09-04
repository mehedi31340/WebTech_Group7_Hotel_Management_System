<?php

require_once "../config/database.php";

require_login();

$id = (int)($_GET['id'] ?? 0);

$gid = guest_id();


/*
|--------------------------------------------------------------------------
| Get booking
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT 
        id,
        total_amount,
        status
    FROM bookings
    WHERE id = ?
      AND guest_id = ?
    LIMIT 1
");

$stmt->bind_param(
    "ii",
    $id,
    $gid
);

$stmt->execute();

$b = $stmt->get_result()->fetch_assoc();

$stmt->close();


/*
|--------------------------------------------------------------------------
| Booking found
|--------------------------------------------------------------------------
*/

if ($b) {

    /*
    |--------------------------------------------------------------------------
    | Check whether loyalty points were already given
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        SELECT id
        FROM loyalty_transactions
        WHERE guest_id = ?
          AND booking_id = ?
          AND points > 0
        LIMIT 1
    ");

    $stmt->bind_param(
        "ii",
        $gid,
        $id
    );

    $stmt->execute();

    $already_earned = $stmt->get_result()->fetch_assoc();

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Mark booking completed
    |--------------------------------------------------------------------------
    */

    $stmt = $conn->prepare("
        UPDATE bookings
        SET status = 'completed'
        WHERE id = ?
          AND guest_id = ?
    ");

    $stmt->bind_param(
        "ii",
        $id,
        $gid
    );

    $stmt->execute();

    $stmt->close();


    /*
    |--------------------------------------------------------------------------
    | Earn loyalty points only once
    |--------------------------------------------------------------------------
    */

    if (!$already_earned) {

        $points = (int) floor(
            ((float)$b['total_amount']) / 100
        );

        if ($points > 0) {

            $desc = "Earned for completed stay " . $id;

            $stmt = $conn->prepare("
                INSERT INTO loyalty_transactions
                (
                    guest_id,
                    booking_id,
                    points,
                    description
                )
                VALUES (?, ?, ?, ?)
            ");

            $stmt->bind_param(
                "iiis",
                $gid,
                $id,
                $points,
                $desc
            );

            $stmt->execute();

            $stmt->close();
        }
    }
}


/*
|--------------------------------------------------------------------------
| Redirect to public index
|--------------------------------------------------------------------------
*/

header("Location: ../public/index.php?page=bookings");

exit;

?>
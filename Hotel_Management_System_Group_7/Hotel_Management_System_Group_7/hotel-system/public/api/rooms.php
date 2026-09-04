<?php

require_once "../../config/database.php";

header('Content-Type: application/json');


/*
|--------------------------------------------------------------------------
| Get Search Parameters
|--------------------------------------------------------------------------
*/

$checkin = $_GET['checkin'] ?? '';

$checkout = $_GET['checkout'] ?? '';

$guests = max(
    1,
    (int)($_GET['guests'] ?? 1)
);


/*
|--------------------------------------------------------------------------
| Validate Dates
|--------------------------------------------------------------------------
*/

if (
    !$checkin ||
    !$checkout ||
    strtotime($checkout) <= strtotime($checkin)
) {

    echo json_encode([
        "ok" => false,
        "message" => "Please select valid check-in and check-out dates."
    ]);

    exit;
}


/*
|--------------------------------------------------------------------------
| Calculate Nights
|--------------------------------------------------------------------------
*/

$nights = (int)(
    (strtotime($checkout) - strtotime($checkin)) / 86400
);


/*
|--------------------------------------------------------------------------
| Get Room Types
|--------------------------------------------------------------------------
*/

$stmt = $conn->prepare("
    SELECT
        rt.*,

        (
            SELECT COUNT(*)
            FROM rooms rm
            WHERE rm.room_type_id = rt.id
              AND rm.status = 'available'
        ) AS total_rooms,

        (
            SELECT COUNT(*)
            FROM bookings b
            WHERE b.room_type_id = rt.id
              AND b.status IN ('confirmed', 'upcoming')
              AND b.check_in < ?
              AND b.check_out > ?
        ) AS booked

    FROM room_types rt

    WHERE rt.capacity >= ?

    ORDER BY rt.price_per_night
");


if (!$stmt) {

    echo json_encode([
        "ok" => false,
        "message" => "Database query error: " . $conn->error
    ]);

    exit;
}


$stmt->bind_param(
    "ssi",
    $checkout,
    $checkin,
    $guests
);


$stmt->execute();

$res = $stmt->get_result();


/*
|--------------------------------------------------------------------------
| Prepare Room Data
|--------------------------------------------------------------------------
*/

$data = [];


while ($r = $res->fetch_assoc()) {

    $available = max(
        0,
        (int)$r['total_rooms'] - (int)$r['booked']
    );


    /*
    |--------------------------------------------------------------------------
    | Image
    |--------------------------------------------------------------------------
    |
    | IMPORTANT:
    | Database value should be:
    |
    | image/room1.jpg
    | image/room2.jpg
    | image/room3.jpg
    |
    */

    $image = trim($r['image'] ?? 'abc');


    /*
    |--------------------------------------------------------------------------
    | Room Data
    |--------------------------------------------------------------------------
    */

    $data[] = [

        "id" => (int)$r['id'],

        "name" => $r['name'],

        "description" => $r['description'],

        "capacity" => (int)$r['capacity'],

        "price" => (float)$r['price_per_night'],

        "nights" => $nights,

        "total" =>
            (float)$r['price_per_night'] * $nights,

        "available" => $available,

        "image" => $image,

        "amenities" => $r['amenities']

    ];
}


$stmt->close();


/*
|--------------------------------------------------------------------------
| Seasonal Pricing Notice
|--------------------------------------------------------------------------
*/

$notice = "";


$stmt = $conn->prepare("
    SELECT
        title,
        description,
        percentage

    FROM seasonal_pricing

    WHERE active = 1

      AND start_date <= ?

      AND end_date >= ?

    LIMIT 1
");


if ($stmt) {

    $stmt->bind_param(
        "ss",
        $checkout,
        $checkin
    );


    $stmt->execute();


    $s = $stmt
        ->get_result()
        ->fetch_assoc();


    if ($s) {

        $notice = [

            "title" =>
                $s['title'],

            "description" =>
                $s['description'],

            "percentage" =>
                (float)$s['percentage']

        ];

    }


    $stmt->close();
}


/*
|--------------------------------------------------------------------------
| Return JSON
|--------------------------------------------------------------------------
*/

echo json_encode([

    "ok" => true,

    "nights" => $nights,

    "rooms" => $data,

    "seasonal_notice" => $notice

]);

exit;

?>
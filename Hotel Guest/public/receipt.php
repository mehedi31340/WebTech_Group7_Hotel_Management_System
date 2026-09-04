<?php

require_once "../config/database.php";
require_login();

$id = (int)($_GET['id'] ?? 0);

$gid = guest_id();

$stmt = $conn->prepare("
    SELECT 
        i.*,
        b.*,
        r.name AS room_name,
        g.full_name,
        g.email
    FROM invoices i
    JOIN bookings b 
        ON b.id = i.booking_id
    JOIN room_types r 
        ON r.id = b.room_type_id
    JOIN guests g 
        ON g.id = b.guest_id
    WHERE i.id = ? 
      AND g.id = ?
");

if (!$stmt) {
    die("Database query preparation failed: " . $conn->error);
}

$stmt->bind_param(
    "ii",
    $id,
    $gid
);

if (!$stmt->execute()) {
    die("Database query failed: " . $stmt->error);
}

$result = $stmt->get_result();

$r = $result->fetch_assoc();

$stmt->close();

if (!$r) {
    die("Receipt not found or you do not have permission to view this receipt.");
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?= e($r['invoice_no']) ?> - Receipt
    </title>

    <link rel="stylesheet" href="css/style.css">

    <style>

        body {
            background: #f4f7f6;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 40px 20px;
        }

        .receipt {
            max-width: 700px;
            margin: 0 auto;
            background: #ffffff;
            padding: 40px;
            border-radius: 18px;
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.08);
        }

        .receipt-head {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .receipt-head strong {
            font-size: 28px;
            color: #183b35;
        }

        .receipt-head span {
            background: #dff5e9;
            color: #198754;
            padding: 8px 18px;
            border-radius: 30px;
            font-weight: bold;
            font-size: 13px;
        }

        .receipt h1 {
            margin-bottom: 5px;
            color: #183b35;
        }

        .invoice-number {
            color: #777;
            margin-bottom: 25px;
        }

        .receipt hr {
            border: 0;
            border-top: 1px solid #e5e5e5;
            margin: 25px 0;
        }

        .receipt-info {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin-top: 20px;
        }

        .receipt-info div {
            background: #f8faf9;
            padding: 15px;
            border-radius: 10px;
        }

        .receipt-info small {
            display: block;
            color: #777;
            margin-bottom: 6px;
        }

        .receipt-info strong {
            color: #222;
        }

        .receipt-total {
            margin-top: 30px;
            padding: 20px;
            background: #183b35;
            color: white;
            border-radius: 14px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .receipt-total span {
            font-size: 16px;
        }

        .receipt-total strong {
            font-size: 24px;
        }

        .receipt-actions {
            display: flex;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            border: 0;
            cursor: pointer;
            padding: 12px 20px;
            border-radius: 10px;
            background: #183b35;
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .btn.secondary {
            background: #e9eeee;
            color: #183b35;
        }

        @media (max-width: 600px) {

            body {
                padding: 20px 12px;
            }

            .receipt {
                padding: 25px 18px;
            }

            .receipt-info {
                grid-template-columns: 1fr;
            }

            .receipt-total {
                flex-direction: column;
                align-items: flex-start;
                gap: 8px;
            }

            .receipt-actions {
                flex-direction: column;
            }

            .btn {
                text-align: center;
            }

        }

        @media print {

            body {
                background: white;
                padding: 0;
            }

            .receipt {
                box-shadow: none;
                max-width: 100%;
                border-radius: 0;
            }

            .receipt-actions {
                display: none;
            }

        }

    </style>

</head>

<body>

    <div class="receipt">

        <div class="receipt-head">

            <strong>HotelGuest</strong>

            <span>PAID</span>

        </div>

        <h1>Receipt</h1>

        <p class="invoice-number">
            Invoice:
            <strong>
                <?= e($r['invoice_no']) ?>
            </strong>
        </p>

        <hr>

        <div class="receipt-info">

            <div>

                <small>Guest Name</small>

                <strong>
                    <?= e($r['full_name']) ?>
                </strong>

            </div>

            <div>

                <small>Email</small>

                <strong>
                    <?= e($r['email']) ?>
                </strong>

            </div>

            <div>

                <small>Room Type</small>

                <strong>
                    <?= e($r['room_name']) ?>
                </strong>

            </div>

            <div>

                <small>Booking ID</small>

                <strong>
                    <?= e($r['booking_code'] ?? $r['booking_id']) ?>
                </strong>

            </div>

            <div>

                <small>Check-in</small>

                <strong>
                    <?= e($r['check_in']) ?>
                </strong>

            </div>

            <div>

                <small>Check-out</small>

                <strong>
                    <?= e($r['check_out']) ?>
                </strong>

            </div>

        </div>

        <hr>

        <div class="receipt-total">

            <span>
                Total Paid
            </span>

            <strong>
                <?= money($r['amount']) ?>
            </strong>

        </div>

        <div class="receipt-actions">

            <button
                class="btn"
                onclick="window.print()">
                Print / Save PDF
            </button>

            <a
                class="btn secondary"
                href="index.php?page=billing">
                Back to Billing
            </a>

        </div>

    </div>

</body>

</html>
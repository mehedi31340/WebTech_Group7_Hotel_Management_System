HOTEL RECEPTIONIST SYSTEM - FINAL

1. Copy the hotel-receptionist-simple folder to C:\xampp\htdocs\
2. Start Apache and MySQL in XAMPP.
3. Open phpMyAdmin and import database.sql.
   WARNING: database.sql deletes and recreates hotel_simple. It is the one
   complete schema for this app (tables, foreign keys, and demo data) — do
   not build a separate/older database first, just import this file.
4. Open: http://localhost/hotel-receptionist-simple/View/login.php
5. Demo login:
   Username: receptionist
   Password: 123456

NEW BOOKING
- Open "New Booking" from the dashboard.
- Select an Available room and fill in the guest's details.
- Choose one of two actions:
  - "Book & Check In" — books the room and checks the guest in immediately.
    A payment method is required and the bill is marked Paid right away.
    The room becomes Occupied.
  - "Book Only" — reserves the room for the guest without checking them in.
    No payment is collected yet (Payment Required). The room becomes
    Reserved, so it won't be offered to another walk-in until this guest
    checks in or the booking is deleted.
- A reservation made with "Book Only" later appears in the Bookings table
  with a Check In control: pick a payment method there and submit to check
  the guest in. That charges the bill and marks it Paid at that moment,
  same as the instant walk-in flow.

PAYMENT
- Guests must pay at check-in — whether that happens instantly (Book &
  Check In) or later, when checking in an existing reservation.
- If a service request is added to a bill that's already Paid, the bill
  automatically flips back to Payment Required for the new balance. Use
  the Payment panel to collect the difference.
- Checkout is blocked until the bill shows Paid.

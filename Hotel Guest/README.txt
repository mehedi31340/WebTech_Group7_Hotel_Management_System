HOTEL SYSTEM - GUEST MODULE
============================

Requested MVC structure:

hotel-system/
├── config/database.php
├── models/
├── views/
├── controllers/
├── public/
│   ├── index.php
│   ├── css/
│   ├── js/
│   └── api/
├── db.sql
└── index.php

SETUP
-----
1. Put the folder in:
   C:\xampp\htdocs\hotel-system

2. Start Apache and MySQL in XAMPP.

3. Open phpMyAdmin and create/import the database:
   hotelguest
   Import: db.sql

4. Open:
   http://localhost/hotel-system/

5. The root index.php redirects to:
   public/index.php

DEMO
----
Email: demo@hotelguest.test
Password: Demo@123

CURRENCY
--------
All booking/payment values use Bangladeshi Taka (BDT / ৳).

NOTE
----
The UI/front controller is kept in public/index.php so the existing guest
features remain immediately testable. Models and controllers are separated
under their requested folders for the project structure and future expansion.

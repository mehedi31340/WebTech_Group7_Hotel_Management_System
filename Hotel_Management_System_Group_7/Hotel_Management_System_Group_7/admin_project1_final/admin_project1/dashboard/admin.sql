CREATE DATABASE IF NOT EXISTS hoteldb;
USE hoteldb;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    nationality VARCHAR(50),
    id_number VARCHAR(100),
    role ENUM('guest', 'receptionist', 'housekeeping', 'admin') NOT NULL DEFAULT 'guest',
    profile_pic VARCHAR(255),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS room_type (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    price_per_night DECIMAL(10,2) NOT NULL,
    max_capacity INT NOT NULL,
    thumbnail_path VARCHAR(255),
    amenities JSON
);

CREATE TABLE IF NOT EXISTS rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT NOT NULL,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    floor INT NOT NULL,
    status ENUM('available','occupied','dirty','in_progress','maintenance','blocked') NOT NULL DEFAULT 'available',
    notes TEXT,
    CONSTRAINT fk_rooms_room_type FOREIGN KEY (room_type_id)
        REFERENCES room_type(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_id INT NOT NULL,
    room_id INT,
    room_type_id INT NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    source ENUM('online','walk-in') NOT NULL DEFAULT 'online',
    status ENUM('pending','confirmed','cancelled','completed','checked_in','checked_out') NOT NULL DEFAULT 'pending',
    total_price DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_bookings_guest FOREIGN KEY (guest_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_bookings_room FOREIGN KEY (room_id) REFERENCES rooms(id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_bookings_room_type FOREIGN KEY (room_type_id) REFERENCES room_type(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    guest_id INT NOT NULL,
    overall_rating DECIMAL(2,1) NOT NULL,
    cleanliness_rating DECIMAL(2,1) NOT NULL,
    service_rating DECIMAL(2,1) NOT NULL,
    review_text TEXT,
    admin_reply TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_reviews_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_reviews_guest FOREIGN KEY (guest_id) REFERENCES users(id) ON DELETE RESTRICT ON UPDATE CASCADE
);

INSERT INTO room_type (name, description, price_per_night, max_capacity)
SELECT 'Standard', 'Comfortable standard room', 80.00, 2
WHERE NOT EXISTS (SELECT 1 FROM room_type WHERE name='Standard');

INSERT INTO room_type (name, description, price_per_night, max_capacity)
SELECT 'Deluxe', 'Spacious deluxe room', 130.00, 2
WHERE NOT EXISTS (SELECT 1 FROM room_type WHERE name='Deluxe');

INSERT INTO room_type (name, description, price_per_night, max_capacity)
SELECT 'Suite', 'Suite with living area', 220.00, 4
WHERE NOT EXISTS (SELECT 1 FROM room_type WHERE name='Suite');

INSERT INTO room_type (name, description, price_per_night, max_capacity)
SELECT 'Executive', 'Top-floor executive room', 300.00, 2
WHERE NOT EXISTS (SELECT 1 FROM room_type WHERE name='Executive');

INSERT INTO rooms (room_type_id, room_number, floor, status)
SELECT rt.id, '101', 1, 'available' FROM room_type rt WHERE rt.name='Standard'
AND NOT EXISTS (SELECT 1 FROM rooms WHERE room_number='101');

INSERT INTO rooms (room_type_id, room_number, floor, status)
SELECT rt.id, '203', 2, 'occupied' FROM room_type rt WHERE rt.name='Deluxe'
AND NOT EXISTS (SELECT 1 FROM rooms WHERE room_number='203');

INSERT INTO rooms (room_type_id, room_number, floor, status)
SELECT rt.id, '312', 3, 'occupied' FROM room_type rt WHERE rt.name='Deluxe'
AND NOT EXISTS (SELECT 1 FROM rooms WHERE room_number='312');

INSERT INTO rooms (room_type_id, room_number, floor, status)
SELECT rt.id, '407', 4, 'maintenance' FROM room_type rt WHERE rt.name='Suite'
AND NOT EXISTS (SELECT 1 FROM rooms WHERE room_number='407');

INSERT INTO rooms (room_type_id, room_number, floor, status)
SELECT rt.id, '502', 5, 'blocked' FROM room_type rt WHERE rt.name='Executive'
AND NOT EXISTS (SELECT 1 FROM rooms WHERE room_number='502');

INSERT INTO users (name, email, password_hash, phone, role, is_active)
SELECT 'ZELO Admin', 'admin@zelo.com', '$2y$12$sAB88JZC5ReqBX6LVELJHePwhAo5O2FJW18alisXL5fwbitFowJ/.', '', 'admin', TRUE
WHERE NOT EXISTS (SELECT 1 FROM users WHERE email='admin@zelo.com');

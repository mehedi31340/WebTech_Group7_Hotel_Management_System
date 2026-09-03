-- Hotel Receptionist System — full schema (single source of truth).
-- Import this file fresh; it drops and recreates hotel_simple.
--
-- Status values used by the application (all plain VARCHAR columns, no
-- enum type, so no migration is needed when new values are introduced):
--   rooms.status            Available | Reserved | Occupied | Dirty
--     Reserved = held for a "Book Only" reservation that hasn't checked
--     in yet; kept separate from Occupied so the two are distinguishable
--     on the Room Status board, and separate from Available so the room
--     can't be double-booked.
--   bookings.status         Booked | Checked In | Checked Out
--   billing.payment_status  pending | paid
--     Shown to the receptionist as "Payment Required" / "Paid". Guests
--     must pay at check-in (both for an instant walk-in and for checking
--     in an existing reservation); if a service charge is added after the
--     bill was paid, it automatically flips back to pending.
--   service_requests.status   pending | in_progress | completed
--   early_late_requests.status  pending | approved | declined

DROP DATABASE IF EXISTS hotel_simple;
CREATE DATABASE hotel_simple CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hotel_simple;

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'receptionist',
    email VARCHAR(120) NULL UNIQUE,
    phone VARCHAR(30) NULL,
    nationality VARCHAR(50) NULL,
    id_number VARCHAR(50) NULL UNIQUE,
    date_of_birth DATE NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_no VARCHAR(20) NOT NULL UNIQUE,
    room_type VARCHAR(50) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Available',
    notes VARCHAR(255) NULL
);

CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_name VARCHAR(100) NOT NULL,
    phone VARCHAR(30) NOT NULL,
    room_id INT NOT NULL,
    checkin DATE NOT NULL,
    checkout DATE NOT NULL,
    guests INT NOT NULL DEFAULT 1,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'Booked',
    source VARCHAR(20) NOT NULL DEFAULT 'online',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    special_request VARCHAR(255) NULL,
    CONSTRAINT fk_bookings_room FOREIGN KEY (room_id) REFERENCES rooms(id)
);

CREATE TABLE billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL UNIQUE,
    base_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    extras_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    total_amount DECIMAL(10,2) NOT NULL DEFAULT 0,
    payment_method VARCHAR(30) NULL,
    payment_status VARCHAR(20) NOT NULL DEFAULT 'pending',
    paid_at DATETIME NULL,
    CONSTRAINT fk_billing_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE TABLE service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    room_id INT NOT NULL,
    service_type VARCHAR(30) NOT NULL,
    description VARCHAR(255) NULL,
    price DECIMAL(10,2) NOT NULL DEFAULT 0,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    requested_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_service_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    CONSTRAINT fk_service_room FOREIGN KEY (room_id) REFERENCES rooms(id)
);

CREATE TABLE early_late_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    request_type VARCHAR(20) NOT NULL,
    requested_time DATETIME NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_early_late_booking FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

INSERT INTO rooms (room_no, room_type, price, status) VALUES
('101','Standard',2500,'Available'),
('102','Standard',2500,'Available'),
('201','Deluxe',4000,'Available'),
('202','Deluxe',4000,'Available'),
('301','Suite',6500,'Available'),
('302','Suite',6500,'Available');

INSERT INTO users (name, username, password, role) VALUES
('Hotel Receptionist','receptionist',
'$2y$12$HQaFjqFROSuyGjB/bMNNou2Pb.D6D1/B9NA9zR5LlSmsArVaSrvO6',
'receptionist');

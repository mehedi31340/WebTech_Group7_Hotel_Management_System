CREATE DATABASE IF NOT EXISTS hotelguest CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE hotelguest;

SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS modification_requests, service_requests, reviews, loyalty_transactions, invoices, bookings, rooms, seasonal_pricing, room_types, guests;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE guests (
 id INT AUTO_INCREMENT PRIMARY KEY,
 full_name VARCHAR(120) NOT NULL,
 email VARCHAR(150) NOT NULL UNIQUE,
 phone VARCHAR(30) NOT NULL,
 nationality VARCHAR(80) NOT NULL,
 id_number VARCHAR(80) NOT NULL UNIQUE,
 password_hash VARCHAR(255) NOT NULL,
 profile_picture VARCHAR(255) NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE room_types (
 id INT AUTO_INCREMENT PRIMARY KEY,
 name VARCHAR(100) NOT NULL,
 description TEXT NOT NULL,
 capacity INT NOT NULL,
 price_per_night DECIMAL(10,2) NOT NULL,
 amenities TEXT NOT NULL,
 image VARCHAR(500) NOT NULL
);

CREATE TABLE rooms (
 id INT AUTO_INCREMENT PRIMARY KEY,
 room_type_id INT NOT NULL,
 room_number VARCHAR(20) NOT NULL UNIQUE,
 status ENUM('available','maintenance','inactive') DEFAULT 'available',
 FOREIGN KEY(room_type_id) REFERENCES room_types(id) ON DELETE CASCADE
);

CREATE TABLE seasonal_pricing (
 id INT AUTO_INCREMENT PRIMARY KEY,
 title VARCHAR(150) NOT NULL,
 description VARCHAR(255) NOT NULL,
 start_date DATE NOT NULL,
 end_date DATE NOT NULL,
 percentage DECIMAL(5,2) DEFAULT 0,
 active TINYINT(1) DEFAULT 1
);

CREATE TABLE bookings (
 id INT AUTO_INCREMENT PRIMARY KEY,
 guest_id INT NOT NULL,
 room_type_id INT NOT NULL,
 booking_code VARCHAR(40) NOT NULL UNIQUE,
 check_in DATE NOT NULL,
 check_out DATE NOT NULL,
 guests INT NOT NULL,
 special_requests TEXT,
 subtotal DECIMAL(10,2) NOT NULL,
 loyalty_discount DECIMAL(10,2) DEFAULT 0,
 total_amount DECIMAL(10,2) NOT NULL,
 status ENUM('confirmed','upcoming','completed','cancelled') DEFAULT 'confirmed',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(guest_id) REFERENCES guests(id) ON DELETE CASCADE,
 FOREIGN KEY(room_type_id) REFERENCES room_types(id)
);

CREATE TABLE invoices (
 id INT AUTO_INCREMENT PRIMARY KEY,
 booking_id INT NOT NULL,
 invoice_no VARCHAR(50) NOT NULL UNIQUE,
 amount DECIMAL(10,2) NOT NULL,
 payment_status ENUM('paid','pending','failed') DEFAULT 'pending',
 issued_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE TABLE service_requests (
 id INT AUTO_INCREMENT PRIMARY KEY,
 guest_id INT NOT NULL,
 booking_id INT NOT NULL,
 service_type ENUM('Extra Bed','Toiletries','Laundry','Room Service','Other') NOT NULL,
 quantity INT DEFAULT 1,
 notes TEXT,
 status ENUM('pending','in_progress','completed','cancelled') DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(guest_id) REFERENCES guests(id) ON DELETE CASCADE,
 FOREIGN KEY(booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE TABLE reviews (
 id INT AUTO_INCREMENT PRIMARY KEY,
 guest_id INT NOT NULL,
 booking_id INT NOT NULL UNIQUE,
 overall_rating TINYINT NOT NULL,
 cleanliness_rating TINYINT NOT NULL,
 service_rating TINYINT NOT NULL,
 comment TEXT,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(guest_id) REFERENCES guests(id) ON DELETE CASCADE,
 FOREIGN KEY(booking_id) REFERENCES bookings(id) ON DELETE CASCADE
);

CREATE TABLE loyalty_transactions (
 id INT AUTO_INCREMENT PRIMARY KEY,
 guest_id INT NOT NULL,
 booking_id INT NULL,
 points INT NOT NULL,
 description VARCHAR(255) NOT NULL,
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(guest_id) REFERENCES guests(id) ON DELETE CASCADE,
 FOREIGN KEY(booking_id) REFERENCES bookings(id) ON DELETE SET NULL
);

CREATE TABLE modification_requests (
 id INT AUTO_INCREMENT PRIMARY KEY,
 booking_id INT NOT NULL,
 guest_id INT NOT NULL,
 new_check_in DATE NOT NULL,
 new_check_out DATE NOT NULL,
 reason TEXT,
 status ENUM('pending','approved','rejected') DEFAULT 'pending',
 created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 FOREIGN KEY(booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
 FOREIGN KEY(guest_id) REFERENCES guests(id) ON DELETE CASCADE
);

INSERT INTO room_types(name,description,capacity,price_per_night,amenities,image) VALUES
('Deluxe Room','Comfortable room with modern amenities and a city-view option.',2,6500,'Free Wi-Fi · Air Conditioning · Smart TV · Mini Bar · Tea/Coffee Maker · Private Bathroom · Room Service','assets/room-deluxe.svg'),
('Executive Room','Premium room designed for business and leisure travellers.',3,9000,'Free Wi-Fi · Air Conditioning · Smart TV · Work Desk · Mini Bar · Breakfast · Room Service','assets/room-executive.svg'),
('Family Suite','Spacious suite for families with separate sleeping and living areas.',4,12500,'Free Wi-Fi · Two Bedrooms · Living Area · Air Conditioning · Smart TV · Breakfast · 24/7 Housekeeping','assets/room-family.svg');

INSERT INTO rooms(room_type_id,room_number,status) VALUES
(1,'101','available'),(1,'102','available'),(1,'103','available'),
(2,'201','available'),(2,'202','available'),
(3,'301','available');

INSERT INTO seasonal_pricing(title,description,start_date,end_date,percentage,active)
VALUES ('Eid & Holiday Season','Selected dates fall within a holiday pricing period. Rates may be higher than regular dates.', '2026-08-20','2026-09-10',10,1);

-- Demo guest: email demo@hotelguest.test, password Demo@123
INSERT INTO guests(full_name,email,phone,nationality,id_number,password_hash)
VALUES ('Demo Guest','demo@hotelguest.test','01712345678','Bangladeshi','DEMO123456',
'$2y$12$FDTTM2uyLsathUuE6fMU/eRn5hb8akvzbMXDahhFESrtB7uAwKzNi');

-- Demo loyalty points are added after registration/login too; this row is just seed structure.

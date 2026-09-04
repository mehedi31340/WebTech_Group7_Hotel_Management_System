CREATE DATABASE hotel_management;

USE hotel_management;
SELECT DATABASE();

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    nationality VARCHAR(50),
    id_number VARCHAR(100),
    role ENUM('guest', 'receptionist', 'housekeeping', 'admin') NOT NULL,
    profile_pic VARCHAR(255),
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

/*DESCRIBE users;*/

CREATE TABLE room_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    description TEXT,
    price_per_night DECIMAL(10,2) NOT NULL,
    max_capacity INT NOT NULL,
    thumbnail_path VARCHAR(255),
    amenities JSON
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT NOT NULL,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    floor INT NOT NULL,
    status ENUM(
        'available',
        'occupied',
        'dirty',
        'in_progress',
        'maintenance',
        'blocked'
    ) NOT NULL DEFAULT 'available',
    notes TEXT,

    CONSTRAINT fk_rooms_room_type
        FOREIGN KEY (room_type_id)
        REFERENCES room_types(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);


CREATE TABLE room_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT NOT NULL,
    image_path VARCHAR(255) NOT NULL,

    CONSTRAINT fk_room_images_room_type
        FOREIGN KEY (room_type_id)
        REFERENCES room_types(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE seasonal_pricing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_type_id INT NOT NULL,
    label VARCHAR(100) NOT NULL,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    price_per_night DECIMAL(10,2) NOT NULL,

    CONSTRAINT fk_seasonal_pricing_room_type
        FOREIGN KEY (room_type_id)
        REFERENCES room_types(id)
        ON DELETE CASCADE
        ON UPDATE CASCADE
);


CREATE TABLE bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_id INT NOT NULL,
    room_id INT,
    room_type_id INT NOT NULL,
    checkin_date DATE NOT NULL,
    checkout_date DATE NOT NULL,
    num_guests INT NOT NULL,
    total_price DECIMAL(10,2) NOT NULL,
    status ENUM(
        'pending',
        'confirmed',
        'checked_in',
        'checked_out',
        'cancelled'
    ) NOT NULL DEFAULT 'pending',
    source ENUM(
        'online',
        'walk_in'
    ) NOT NULL DEFAULT 'online',
    special_requests TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_bookings_guest
        FOREIGN KEY (guest_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_bookings_room
        FOREIGN KEY (room_id)
        REFERENCES rooms(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_bookings_room_type
        FOREIGN KEY (room_type_id)
        REFERENCES room_types(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE billing (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    guest_id INT NOT NULL,
    base_amount DECIMAL(10,2) NOT NULL,
    extras_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    discount_amount DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    total_amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    payment_status ENUM('pending', 'paid') NOT NULL DEFAULT 'pending',
    paid_at DATETIME,
    receipt_path VARCHAR(255),

    CONSTRAINT fk_billing_booking
        FOREIGN KEY (booking_id)
        REFERENCES bookings(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_billing_guest
        FOREIGN KEY (guest_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE service_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    guest_id INT NOT NULL,
    room_id INT NOT NULL,
    service_type ENUM(
        'extra_bed',
        'toiletries',
        'laundry',
        'room_service',
        'other'
    ) NOT NULL,
    description TEXT,
    status ENUM(
        'pending',
        'in_progress',
        'completed'
    ) NOT NULL DEFAULT 'pending',
    requested_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_service_requests_booking
        FOREIGN KEY (booking_id)
        REFERENCES bookings(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_service_requests_guest
        FOREIGN KEY (guest_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_service_requests_room
        FOREIGN KEY (room_id)
        REFERENCES rooms(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE housekeeping_tasks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    assigned_to INT NOT NULL,
    task_type ENUM(
        'cleaning',
        'inspection',
        'maintenance'
    ) NOT NULL,
    priority ENUM(
        'normal',
        'urgent'
    ) NOT NULL DEFAULT 'normal',
    status ENUM(
        'pending',
        'in_progress',
        'done'
    ) NOT NULL DEFAULT 'pending',
    notes TEXT,
    scheduled_date DATE NOT NULL,
    completed_at DATETIME,

    CONSTRAINT fk_housekeeping_tasks_room
        FOREIGN KEY (room_id)
        REFERENCES rooms(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_housekeeping_tasks_assigned_to
        FOREIGN KEY (assigned_to)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

   CREATE TABLE maintenance_reports (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_id INT NOT NULL,
    reported_by INT NOT NULL,
    description TEXT NOT NULL,
    severity ENUM(
        'low',
        'medium',
        'high'
    ) NOT NULL DEFAULT 'medium',
    status ENUM(
        'open',
        'in_progress',
        'resolved'
    ) NOT NULL DEFAULT 'open',
    reported_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    resolved_at DATETIME,

    CONSTRAINT fk_maintenance_reports_room
        FOREIGN KEY (room_id)
        REFERENCES rooms(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_maintenance_reports_reported_by
        FOREIGN KEY (reported_by)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    booking_id INT NOT NULL,
    guest_id INT NOT NULL,
    overall_rating TINYINT NOT NULL,
    cleanliness_rating TINYINT NOT NULL,
    service_rating TINYINT NOT NULL,
    review_text TEXT,
    admin_reply TEXT,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT uq_reviews_booking
        UNIQUE (booking_id),

    CONSTRAINT fk_reviews_booking
        FOREIGN KEY (booking_id)
        REFERENCES bookings(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_reviews_guest
        FOREIGN KEY (guest_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT chk_reviews_overall_rating
        CHECK (overall_rating BETWEEN 1 AND 5),

    CONSTRAINT chk_reviews_cleanliness_rating
        CHECK (cleanliness_rating BETWEEN 1 AND 5),

    CONSTRAINT chk_reviews_service_rating
        CHECK (service_rating BETWEEN 1 AND 5)
);

CREATE TABLE loyalty_points (
    id INT AUTO_INCREMENT PRIMARY KEY,
    guest_id INT NOT NULL,
    booking_id INT,
    points_earned INT NOT NULL DEFAULT 0,
    points_used INT NOT NULL DEFAULT 0,
    balance INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_loyalty_points_guest
        FOREIGN KEY (guest_id)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    CONSTRAINT fk_loyalty_points_booking
        FOREIGN KEY (booking_id)
        REFERENCES bookings(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

CREATE TABLE announcements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(150) NOT NULL,
    message TEXT NOT NULL,
    posted_by INT NOT NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_announcements_posted_by
        FOREIGN KEY (posted_by)
        REFERENCES users(id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
);

/*SHOW TABLES;*/

/*SELECT
    TABLE_NAME,
    COLUMN_NAME,
    CONSTRAINT_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'hotel_management'
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME;*/
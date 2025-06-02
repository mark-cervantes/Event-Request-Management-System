-- Corrected init.sql
USE sta_cruz_db;

-- 1. Create officials table (no dependencies)
CREATE TABLE IF NOT EXISTS officials (
    id INT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(50) NOT NULL,
    middle_name VARCHAR(50),
    last_name VARCHAR(50) NOT NULL,
    gender ENUM('Male', 'Female', 'Other', 'Prefer not to say'),
    birth_date DATE,
    position_title VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE,
    contact_number VARCHAR(20),
    start_term DATE NOT NULL,
    end_term DATE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insert sample data for officials (IDs will be 1, 2, 3 due to AUTO_INCREMENT)
INSERT INTO officials (first_name, middle_name, last_name, gender, birth_date, position_title, email, contact_number, start_term, end_term) VALUES
('John', 'Michael', 'Doe', 'Male', '1980-05-15', 'Mayor', 'john.doe@city.gov', '123-456-7890', '2024-01-01', '2028-01-01'),
('Jane', 'Elizabeth', 'Smith', 'Female', '1985-08-22', 'City Council Member', 'jane.smith@city.gov', '987-654-3210', '2024-01-01', NULL),
('Robert', NULL, 'Johnson', 'Male', '1975-12-10', 'City Manager', 'robert.johnson@city.gov', '555-123-4567', '2023-06-01', '2027-06-01');

-- 2. Create resident table
CREATE TABLE IF NOT EXISTS `resident` (
  `resident_id` bigint(20) NOT NULL AUTO_INCREMENT, -- Retain AUTO_INCREMENT for future application inserts
  `first_name` varchar(50) NOT NULL,
  `middle_name` varchar(50) DEFAULT NULL,
  `last_name` varchar(50) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `civil_status` enum('S','M') NOT NULL,
  `household_no` int(11) DEFAULT NULL CHECK (`household_no` BETWEEN 1 AND 9999),
  `religion` varchar(50) DEFAULT NULL,
  `gender` enum('M','F') NOT NULL,
  `birth_date` date DEFAULT NULL,
  `status` enum('active','inactive','archived') NOT NULL DEFAULT 'active',
  PRIMARY KEY (`resident_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; -- Removed AUTO_INCREMENT=18 specification here

-- Insert sample resident data with EXPLICIT IDs to match users table FKs
INSERT INTO resident (resident_id, first_name, middle_name, last_name, contact_number, address, civil_status, household_no, religion, gender, birth_date, status) VALUES
(1, 'Juan', 'Santos', 'Cruz', '09123456789', '123 Main St, Sta. Cruz', 'M', 1001, 'Catholic', 'M', '1985-03-15', 'active'),
(2, 'Maria', 'Dela', 'Santos', '09234567890', '456 Secondary St, Sta. Cruz', 'S', 1002, 'Catholic', 'F', '1990-07-22', 'active'),
(3, 'Pedro', 'Rizal', 'Garcia', '09345678901', '789 Tertiary St, Sta. Cruz', 'M', 1003, 'Protestant', 'M', '1982-11-08', 'active'),
(4, 'Ana', 'Jose', 'Reyes', '09456789012', '321 Fourth St, Sta. Cruz', 'S', 1004, 'Catholic', 'F', '1995-02-14', 'active'),
(5, 'Carlos', 'Miguel', 'Rodriguez', '09567890123', '654 Fifth St, Sta. Cruz', 'M', 1005, 'Catholic', 'M', '1988-09-30', 'active'),
(6, 'Isabel', 'Carmen', 'Fernandez', '09678901234', '987 Sixth St, Sta. Cruz', 'M', 1006, 'Catholic', 'F', '1992-06-18', 'active'),
(7, 'Roberto', 'Antonio', 'Martinez', '09789012345', '147 Seventh St, Sta. Cruz', 'S', 1007, 'Protestant', 'M', '1987-12-03', 'active'),
(8, 'Rosa', 'Elena', 'Lopez', '09890123456', '258 Eighth St, Sta. Cruz', 'M', 1008, 'Catholic', 'F', '1993-04-25', 'active');
-- The next AUTO_INCREMENT value for resident will be 9 if inserted without an explicit ID.

-- 3. Create users table (depends on resident)
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(20) NOT NULL AUTO_INCREMENT, -- Retain AUTO_INCREMENT for future application inserts
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `resident_id` bigint(20) DEFAULT NULL,
  `status` enum('active','inactive','archived') NOT NULL DEFAULT 'active',
  `role` enum('admin','official','user','guest') NOT NULL DEFAULT 'user',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `start_term` date DEFAULT NULL,
  `end_term` date DEFAULT NULL,
  `position_title` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `uq_username` (`username`), -- Added unique constraint for username
  UNIQUE KEY `uq_email` (`email`),       -- Added unique constraint for email (if it should be globally unique)
  KEY `fk_users_resident` (`resident_id`),
  CONSTRAINT `fk_users_resident` FOREIGN KEY (`resident_id`) REFERENCES `resident` (`resident_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci; -- Removed AUTO_INCREMENT=156 specification here

-- Insert sample users data for each role type
-- The resident_id values (1-8) now correctly reference the explicit IDs inserted into the resident table.
-- The user_id values (1-9) are explicit.
INSERT INTO users (user_id, username, password, email, resident_id, status, role, start_term, end_term, position_title) VALUES
(1, 'admin', 'admin123', 'admin@stacruz.gov.ph', 1, 'active', 'admin', NULL, NULL, 'System Administrator'),
(2, 'superadmin', 'superadmin123', 'superadmin@stacruz.gov.ph', 2, 'active', 'admin', NULL, NULL, 'Super Administrator'),
(3, 'captain_garcia', 'official123', 'captain@stacruz.gov.ph', 3, 'active', 'official', '2024-01-01', '2027-12-31', 'Barangay Captain'),
(4, 'kagawad_ana', 'official123', 'kagawad1@stacruz.gov.ph', 4, 'active', 'official', '2024-01-01', '2027-12-31', 'Kagawad'),
(5, 'secretary_carlos', 'official123', 'secretary@stacruz.gov.ph', 5, 'active', 'official', '2024-01-01', '2027-12-31', 'Barangay Secretary'),
(6, 'user_isabel', 'user123', 'isabel@stacruz.gov.ph', 6, 'active', 'user', NULL, NULL, 'Staff Member'),
(7, 'user_roberto', 'user123', 'roberto@example.com', 7, 'active', 'user', NULL, NULL, 'Resident'),
(8, 'user_rosa', 'user123', 'rosa@example.com', 8, 'active', 'user', NULL, NULL, 'Resident'),
(9, 'guest_user', 'guest123', 'guest@example.com', NULL, 'active', 'guest', NULL, NULL, 'Guest User');
-- The next AUTO_INCREMENT value for users will be 10 if inserted without an explicit ID.

-- 4. Create reservations table (depends on officials and users)
CREATE TABLE IF NOT EXISTS reservations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT COMMENT 'Internal staff user ID who created request',
    official_id INT NULL COMMENT 'Barangay official assigned to handle request',
    facility ENUM('Barangay Hall', 'Basketball Court', 'Covered Court', 'Multi-purpose Area', 'Auditorium', 'Meeting Room 1', 'Meeting Room 2', 'Training Room A', 'Executive Boardroom', 'Conference Room A', 'Conference Room B', 'Conference Room C') NOT NULL,
    event_name VARCHAR(200) NOT NULL COMMENT 'Name of the event/meeting',
    start_datetime DATETIME NOT NULL COMMENT 'When the event starts',
    end_datetime DATETIME NOT NULL COMMENT 'When the event ends',
    date_time_requested TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'When this request was submitted',
    status ENUM('Pending', 'Approved', 'Rejected', 'Completed', 'Cancelled') DEFAULT 'Pending',
    requested_by VARCHAR(100) NOT NULL COMMENT 'Name of person requesting the facility',
    contact_number VARCHAR(20) COMMENT 'Contact number of requester',
    purpose TEXT COMMENT 'Purpose/description of the event',
    expected_attendees INT DEFAULT 0 COMMENT 'Expected number of attendees',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY fk_reservations_official (official_id),
    KEY fk_reservations_user (user_id),
    CONSTRAINT fk_reservations_official FOREIGN KEY (official_id) REFERENCES officials (id) ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT fk_reservations_user FOREIGN KEY (user_id) REFERENCES users (user_id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Insert sample data for reservations
-- user_id values (6, 7, 1, 8, 2) now correctly reference existing user_ids.
-- official_id values (1, 2, 3) correctly reference existing official_ids.
INSERT INTO reservations (user_id, official_id, facility, event_name, start_datetime, end_datetime, status, requested_by, contact_number, purpose, expected_attendees) VALUES
(6, 1, 'Barangay Hall', 'Community Meeting', '2025-06-01 14:00:00', '2025-06-01 16:00:00', 'Approved', 'Maria Santos', '09123456789', 'Monthly community consultation', 50),
(7, 2, 'Basketball Court', 'Youth Basketball Tournament', '2025-06-15 08:00:00', '2025-06-15 18:00:00', 'Pending', 'Carlos Rodriguez', '09987654321', 'Annual youth sports event', 200),
(1, NULL, 'Meeting Room 1', 'Staff Training', '2025-06-10 09:00:00', '2025-06-10 12:00:00', 'Approved', 'Anna Cruz', '09111222333', 'Employee development training', 15),
(8, 3, 'Covered Court', 'Community Sports Day', '2025-06-20 07:00:00', '2025-06-20 17:00:00', 'Pending', 'Rosa Lopez', '09890123456', 'Annual community sports and recreation event', 150),
(2, 1, 'Auditorium', 'Health Seminar', '2025-06-25 13:00:00', '2025-06-25 17:00:00', 'Approved', 'Dr. Maria Santos', '09234567890', 'Public health awareness seminar', 100);
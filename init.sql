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

-- Insert sample data for officials
INSERT INTO officials (first_name, middle_name, last_name, gender, birth_date, position_title, email, contact_number, start_term, end_term) VALUES
('John', 'Michael', 'Doe', 'Male', '1980-05-15', 'Mayor', 'john.doe@city.gov', '123-456-7890', '2024-01-01', '2028-01-01'),
('Jane', 'Elizabeth', 'Smith', 'Female', '1985-08-22', 'City Council Member', 'jane.smith@city.gov', '987-654-3210', '2024-01-01', NULL),
('Robert', NULL, 'Johnson', 'Male', '1975-12-10', 'City Manager', 'robert.johnson@city.gov', '555-123-4567', '2023-06-01', '2027-06-01');

-- 2. Create resident table
CREATE TABLE IF NOT EXISTS `resident` (
  `resident_id` bigint(20) NOT NULL AUTO_INCREMENT,
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample resident data with EXPLICIT IDs
INSERT INTO resident (resident_id, first_name, middle_name, last_name, contact_number, address, civil_status, household_no, religion, gender, birth_date, status) VALUES
(1, 'Juan', 'Santos', 'Cruz', '09123456789', '123 Main St, Sta. Cruz', 'M', 1001, 'Catholic', 'M', '1985-03-15', 'active'),
(2, 'Maria', 'Dela', 'Santos', '09234567890', '456 Secondary St, Sta. Cruz', 'S', 1002, 'Catholic', 'F', '1990-07-22', 'active'),
(3, 'Pedro', 'Rizal', 'Garcia', '09345678901', '789 Tertiary St, Sta. Cruz', 'M', 1003, 'Protestant', 'M', '1982-11-08', 'active'),
(4, 'Ana', 'Jose', 'Reyes', '09456789012', '321 Fourth St, Sta. Cruz', 'S', 1004, 'Catholic', 'F', '1995-02-14', 'active'),
(5, 'Carlos', 'Miguel', 'Rodriguez', '09567890123', '654 Fifth St, Sta. Cruz', 'M', 1005, 'Catholic', 'M', '1988-09-30', 'active'),
(6, 'Isabel', 'Carmen', 'Fernandez', '09678901234', '987 Sixth St, Sta. Cruz', 'M', 1006, 'Catholic', 'F', '1992-06-18', 'active'),
(7, 'Roberto', 'Antonio', 'Martinez', '09789012345', '147 Seventh St, Sta. Cruz', 'S', 1007, 'Protestant', 'M', '1987-12-03', 'active'),
(8, 'Rosa', 'Elena', 'Lopez', '09890123456', '258 Eighth St, Sta. Cruz', 'M', 1008, 'Catholic', 'F', '1993-04-25', 'active');

-- 3. Create users table (depends on resident)
CREATE TABLE IF NOT EXISTS `users` (
  `user_id` int(20) NOT NULL AUTO_INCREMENT,
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
  UNIQUE KEY `uq_username` (`username`),
  UNIQUE KEY `uq_email` (`email`),
  KEY `fk_users_resident` (`resident_id`),
  CONSTRAINT `fk_users_resident` FOREIGN KEY (`resident_id`) REFERENCES `resident` (`resident_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample users data
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
INSERT INTO reservations (user_id, official_id, facility, event_name, start_datetime, end_datetime, status, requested_by, contact_number, purpose, expected_attendees) VALUES
(6, 1, 'Barangay Hall', 'Community Meeting', '2025-06-01 14:00:00', '2025-06-01 16:00:00', 'Approved', 'Maria Santos', '09123456789', 'Monthly community consultation', 50),
(7, 2, 'Basketball Court', 'Youth Basketball Tournament', '2025-06-15 08:00:00', '2025-06-15 18:00:00', 'Pending', 'Carlos Rodriguez', '09987654321', 'Annual youth sports event', 200),
(1, NULL, 'Meeting Room 1', 'Staff Training', '2025-06-10 09:00:00', '2025-06-10 12:00:00', 'Approved', 'Anna Cruz', '09111222333', 'Employee development training', 15),
(8, 3, 'Covered Court', 'Community Sports Day', '2025-06-20 07:00:00', '2025-06-20 17:00:00', 'Pending', 'Rosa Lopez', '09890123456', 'Annual community sports and recreation event', 150),
(2, 1, 'Auditorium', 'Health Seminar', '2025-06-25 13:00:00', '2025-06-25 17:00:00', 'Approved', 'Dr. Maria Santos', '09234567890', 'Public health awareness seminar', 100);

-- 5. Create business_certificate table (UPDATED to store images in database)
CREATE TABLE IF NOT EXISTS `business_certificate` (
  `business_certificate_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `business_type` varchar(100) DEFAULT NULL,
  `status` int(11) NOT NULL DEFAULT 0,
  `business_name` varchar(255) DEFAULT NULL,
  `business_address` varchar(255) DEFAULT NULL,
  `date_registered` date DEFAULT NULL,
  `issued_date` date DEFAULT NULL,
  `image_data` LONGTEXT DEFAULT NULL COMMENT 'Base64 encoded payment receipt image',
  `expiry_date` date DEFAULT NULL,
  `business_owner` bigint(20) DEFAULT NULL,
  PRIMARY KEY (`business_certificate_id`),
  KEY `fk_business_owner` (`business_owner`),
  CONSTRAINT `fk_business_owner` FOREIGN KEY (`business_owner`) REFERENCES `resident` (`resident_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert sample data for business_certificate (UPDATED to remove path references)
INSERT INTO `business_certificate` (`business_certificate_id`, `business_type`, `status`, `business_name`, `business_address`, `date_registered`, `issued_date`, `image_data`, `expiry_date`, `business_owner`) VALUES
(2, 'Retail', 0, 'S', 'A', '2024-05-01', '2024-05-02', NULL, '2025-05-01', 7),
(3, 'Retail', 1, 'ABC Store', '123 Main Street, Springfield', '2024-05-01', '2024-05-02', NULL, '2025-05-31', 3);

-- 6. Create complaint_form table (CORRECTED with proper constraints)
CREATE TABLE IF NOT EXISTS `complaint_form` (
  `complaint_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `user_id` int(20) DEFAULT NULL,
  `description` varchar(500) DEFAULT NULL,
  `incident_date` date DEFAULT NULL,
  `submission_date` date DEFAULT NULL,
  `attachment_file` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`complaint_id`),
  KEY `fk_complaint_user` (`user_id`),
  CONSTRAINT `fk_complaint_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 7. Create payment table (CORRECTED with proper constraints)
CREATE TABLE IF NOT EXISTS `payment` (
  `payment_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `business_certificate_id` bigint(20) DEFAULT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL CHECK (`amount` between 0.00 and 99999999.99),
  `status` varchar(10) DEFAULT NULL CHECK (`status` in ('paid','unpaid','pending','failed')),
  `upload` varchar(255) DEFAULT NULL,
  `print` tinyint(1) DEFAULT NULL,
  PRIMARY KEY (`payment_id`),
  KEY `fk_payment_business` (`business_certificate_id`),
  CONSTRAINT `fk_payment_business` FOREIGN KEY (`business_certificate_id`) REFERENCES `business_certificate` (`business_certificate_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 8. Create record table (CORRECTED with proper constraints)
CREATE TABLE IF NOT EXISTS `record` (
  `record_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `complaint_id` bigint(20) DEFAULT NULL,
  `official_id` int DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL CHECK (`status` in ('pending','reviewed','resolved','rejected','in_progress')),
  `submission_date` date DEFAULT NULL,
  PRIMARY KEY (`record_id`),
  KEY `fk_record_complaint` (`complaint_id`),
  KEY `fk_record_official` (`official_id`),
  CONSTRAINT `fk_record_complaint` FOREIGN KEY (`complaint_id`) REFERENCES `complaint_form` (`complaint_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_record_official` FOREIGN KEY (`official_id`) REFERENCES `officials` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 9. Create request_transaction table (CORRECTED with proper constraints)
CREATE TABLE IF NOT EXISTS `request_transaction` (
  `request_id` bigint(20) NOT NULL AUTO_INCREMENT,
  `submission_date` date DEFAULT NULL,
  `user_id` int(20) DEFAULT NULL,
  `status` varchar(10) DEFAULT NULL CHECK (`status` in ('pending','approved','rejected','completed')),
  `purpose` varchar(255) DEFAULT NULL,
  `last_updated` date DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `fk_request_user` (`user_id`),
  CONSTRAINT `fk_request_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

CREATE TABLE IF NOT EXISTS `complaints` (
  `brngy_case_no` varchar(50) NOT NULL,
  `case_type` varchar(100) DEFAULT NULL,
  `complainant_fullname` varchar(255) NOT NULL,
  `complainant_address` varchar(255) DEFAULT NULL,
  `complainant_cellphone` varchar(20) DEFAULT NULL,
  `respondent_fullname` varchar(255) DEFAULT NULL,
  `respondent_address` varchar(255) DEFAULT NULL,
  `respondent_cellphone` varchar(20) DEFAULT NULL,
  `complaint_description` text DEFAULT NULL,
  `attachment_file` varchar(255) DEFAULT NULL,
  `date_of_incident` date DEFAULT NULL,
  `submitted_date` timestamp DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','in progress','resolved','rejected') DEFAULT 'pending',
  PRIMARY KEY (`brngy_case_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Insert some sample data
INSERT INTO `complaints` (`brngy_case_no`, `case_type`, `complainant_fullname`, `complainant_address`, `complainant_cellphone`, `respondent_fullname`, `respondent_address`, `respondent_cellphone`, `complaint_description`, `date_of_incident`, `status`) VALUES
('brgy-2025-01', 'Noise Complaint', 'Juan Cruz', '123 Main St, Sta. Cruz', '09123456789', 'Pedro Garcia', '456 Secondary St, Sta. Cruz', '09234567890', 'Loud music during late hours disturbing the neighborhood', '2025-05-28', 'pending'),
('brgy-2025-02', 'Property Dispute', 'Maria Santos', '789 Tertiary St, Sta. Cruz', '09345678901', 'Ana Reyes', '321 Fourth St, Sta. Cruz', '09456789012', 'Boundary dispute between neighboring properties', '2025-05-30', 'in progress'),
('brgy-2025-03', 'Harassment', 'Carlos Rodriguez', '654 Fifth St, Sta. Cruz', '09567890123', 'Roberto Martinez', '147 Seventh St, Sta. Cruz', '09789012345', 'Verbal harassment and threats from neighbor', '2025-06-01', 'resolved'),
('brgy-2025-04', 'Public Nuisance', 'Isabel Fernandez', '987 Sixth St, Sta. Cruz', '09678901234', 'Rosa Lopez', '258 Eighth St, Sta. Cruz', '09890123456', 'Blocking public walkway with personal belongings', '2025-06-02', 'pending');
-- ===================================================================
-- Support Session Manager - Database Schema
-- Use Case 3: Manage Support Session and Provide Feedback
-- Import this file in phpMyAdmin (XAMPP) to create the database.
-- ===================================================================

CREATE DATABASE IF NOT EXISTS support_system;
USE support_system;

-- People using the system
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    role ENUM('student','volunteer') NOT NULL,
    email VARCHAR(150)
);

-- A support request that a volunteer has already accepted
-- (created by the earlier "Request Support" / "Accept Request" use cases)
CREATE TABLE support_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    volunteer_id INT NOT NULL,
    subject VARCHAR(150) NOT NULL,
    status ENUM('Accepted','Scheduled','Completed','Cancelled') NOT NULL DEFAULT 'Accepted',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES users(id),
    FOREIGN KEY (volunteer_id) REFERENCES users(id)
);

-- Time slots each volunteer has made available
CREATE TABLE volunteer_availability (
    id INT AUTO_INCREMENT PRIMARY KEY,
    volunteer_id INT NOT NULL,
    available_date DATE NOT NULL,
    available_time TIME NOT NULL,
    is_booked TINYINT(1) NOT NULL DEFAULT 0,
    FOREIGN KEY (volunteer_id) REFERENCES users(id)
);

-- A scheduled/completed/cancelled session tied to one request
CREATE TABLE sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    request_id INT NOT NULL,
    session_date DATE NOT NULL,
    session_time TIME NOT NULL,
    mode ENUM('Online','In-Person') NOT NULL,
    status ENUM('Scheduled','Completed','Cancelled') NOT NULL DEFAULT 'Scheduled',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (request_id) REFERENCES support_requests(id)
);

-- Feedback the student leaves once a session is completed
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id INT NOT NULL UNIQUE,
    rating TINYINT NOT NULL,
    comments TEXT,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (session_id) REFERENCES sessions(id)
);

-- ---------------------------------------------------------------
-- Sample data so you can test the app immediately
-- ---------------------------------------------------------------
INSERT INTO users (name, role, email) VALUES
('Aisha (Student)',   'student',   'aisha@example.com'),
('Ben (Student)',     'student',   'ben@example.com'),
('Chandra (Volunteer)','volunteer','chandra@example.com'),
('Dinesh (Volunteer)', 'volunteer','dinesh@example.com');

-- Aisha's request has already been accepted by Chandra -> ready to schedule
INSERT INTO support_requests (student_id, volunteer_id, subject, status) VALUES
(1, 3, 'Help with Maths - Algebra basics', 'Accepted');

-- Chandra has a few open time slots
INSERT INTO volunteer_availability (volunteer_id, available_date, available_time, is_booked) VALUES
(3, CURDATE() + INTERVAL 1 DAY, '10:00:00', 0),
(3, CURDATE() + INTERVAL 1 DAY, '11:00:00', 0),
(3, CURDATE() + INTERVAL 2 DAY, '14:00:00', 0),
(3, CURDATE() + INTERVAL 3 DAY, '09:00:00', 0);

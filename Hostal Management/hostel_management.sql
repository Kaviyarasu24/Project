USE hostel;

CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(50) NOT NULL
);

CREATE TABLE courses (
    id INT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL
);

CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL,
    seater VARCHAR(50) NOT NULL,
    fee DECIMAL(10, 2) NOT NULL
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    contact_number VARCHAR(15) NOT NULL,
    emergency_contact VARCHAR(15) NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,
    date_of_birth DATE NOT NULL,
    course_id INT NOT NULL,
    year_of_study INT NOT NULL,
    room VARCHAR(10) NOT NULL,
    registration_date DATE NOT NULL,
    address VARCHAR(255) NOT NULL,
    guardian_name VARCHAR(100) NOT NULL,
    guardian_contact VARCHAR(15) NOT NULL,
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    description TEXT NOT NULL,
    status ENUM('fixed', 'need_to_fix') DEFAULT 'need_to_fix',
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

CREATE TABLE complainthistory (
    id INT AUTO_INCREMENT PRIMARY KEY,
    complaint_id INT,
    status ENUM('fixed', 'need_to_fix'),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (complaint_id) REFERENCES complaints(id) ON DELETE CASCADE
);

CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    accessibility VARCHAR(20),
    mess VARCHAR(20),
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);

INSERT INTO admin (username, password) VALUES ('admin', 'admin');
INSERT INTO courses (id, course_name) VALUES (1, 'B.Tech');
INSERT INTO courses (id, course_name) VALUES (2, 'B.Com');
INSERT INTO courses (id, course_name) VALUES (3, 'Bachelor of Science');
INSERT INTO rooms (room_number, seater, fee) VALUES ('100', '3 Roommate with AC (115000)', 115000.00);
INSERT INTO rooms (room_number, seater, fee) VALUES ('200', '4 Roommate with NON-AC (85000)', 85000.00);
INSERT INTO students (id, name, email, contact_number, emergency_contact, gender, date_of_birth, course_id, year_of_study, room, registration_date, address, guardian_name, guardian_contact) VALUES (1, 'Test Student', 'test@example.com', '1234567890', '0987654321', 'Male', '2000-01-01', 1, 1, '101', '2025-05-13', '123 Test Street', 'Test Guardian', '1122334455');
INSERT INTO feedback (student_id, accessibility, mess) VALUES (1, 'Very Good', 'Excellent');


CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    leave_from DATE NOT NULL,
    leave_to DATE NOT NULL,
    reason TEXT NOT NULL,
    status ENUM('Pending', 'Approved', 'Rejected') DEFAULT 'Pending',
    approved_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE
);


CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT NOT NULL,
    date DATE NOT NULL,
    status ENUM('Present', 'Absent', 'On Leave') NOT NULL,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE (student_id, date)
);


# 🏨 Hostel Management System

<p align="center">
  <img src="https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white" alt="PHP" />
  <img src="https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=whit---

## 📝 Notes

- 🎨 The application uses **Bootstrap 3.3.7** for styling, so ensure an internet connection for CDN links or host Bootstrap locally
- 🔒 Passwords in the database should be hashed using PHP's `password_hash()` function for security
- 🚀 The project can be extended with features like pagination, email notifications, or advanced reporting

---

## 📄 License
This project is for **educational purposes** and does not include a specific license.

---

<p align="center">
  <strong>🏨 Made with ❤️ for efficient hostel management</strong>
</p>

<p align="center">
  <img src="https://img.shields.io/badge/version-1.0.0-blue.svg" alt="Version" />
  <img src="https://img.shields.io/badge/status-active-success.svg" alt="Status" />
  <img src="https://img.shields.io/badge/PHP-7.4+-brightgreen.svg" alt="PHP Version" />
</p>MySQL" />
  <img src="https://img.shields.io/badge/Bootstrap-563D7C?style=for-the-badge&logo=bootstrap&logoColor=white" alt="Bootstrap" />
  <img src="https://img.shields.io/badge/XAMPP-FB7A24?style=for-the-badge&logo=xampp&logoColor=white" alt="XAMPP" />
</p>

## 📋 Overview
The Hostel Management System is a web-based application designed to streamline hostel operations. It provides role-based access for admins and students, allowing admins to manage hostel resources and students to interact with the system for various services.

## ✨ Features

### 👨‍💼 Admin Features:
- 👥 Manage students, courses, and rooms
- 🚨 View and resolve complaints
- 💬 View feedback from students
- ✅ Approve or reject leave requests
- 📅 View attendance for all students by date

### 👨‍🎓 Student Features:
- 📢 Register complaints and submit feedback
- 🏖️ Request leaves and view leave status
- 👀 View personal attendance records
- ✅ Mark their own attendance for a specific date



## 📁 Project Structure
Below is the directory structure of the project:

```
Hostel-Management-System/
│
├── 📁 admin/                      
│   ├── 🔐 adminpage.php         
│   ├── 📊 dashboard.php          
│   ├── ➕ add-course.php         
│   ├── 📚 manage-courses.php      
│   ├── 🏠 add-room.php           
│   ├── 🛏️ manage-rooms.php        
│   ├── 👥 manage-students.php     
│   ├── 🚨 complaints.php          
│   ├── 💬 feedback.php            
│   ├── ✅ leave-approval.php      
│   ├── 📝 mark-attendance.php    
│   └── 📅 view-all-attendance.php 
│
├── 📁 user/                       
│   ├── 🔑 userlogin.php          
│   ├── 🏠 dashboard.php          
│   ├── 📢 register-complaint.php 
│   ├── 💭 feedback.php            
│   ├── 🏖️ leave-request.php       
│   ├── 👀 view-attendance.php     
│   ├── ✅ mark-attendance.php     
│   └── 🚪 logout.php              
│
├── 📁 includes/                   
│   └── 🔗 dbconn.php             
│
├── 📁 css/                        
│   └── 🎨 style.css               
│
└── 🏠 index.php                   
```

## 🔧 Prerequisites

| Requirement | Purpose |
|-------------|---------|
| **🔥 XAMPP** | To run the PHP application and MySQL database |
| **🌐 Web Browser** | For accessing the application (e.g., Chrome, Firefox) |
| **📝 Text Editor** | For editing code (e.g., VS Code, Sublime Text) |

## ⚙️ Setup Instructions

### 1️⃣ Install XAMPP

- 📥 Download and install XAMPP from https://www.apachefriends.org/
- ▶️ Start the **Apache** and **MySQL** modules from the XAMPP Control Panel

### 2️⃣ Set Up the Project

**📂 Clone or Copy the Project:**
> Place the project folder (Hostel-Management-System) inside the htdocs directory of your XAMPP installation (e.g., `C:\xampp\htdocs\Hostel-Management-System`)

**🗄️ Configure the Database:**
1. 🌐 Open a web browser and go to http://localhost/phpmyadmin
2. 🆕 Create a new database named `hostel_management`
3. 📋 Run the following SQL script to create the necessary tables:



```sql
-- 👨‍💼 Create admin table
CREATE TABLE admin (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL
);

-- 👨‍🎓 Create students table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    contact_number VARCHAR(15),
    emergency_contact VARCHAR(15),
    gender VARCHAR(10),
    date_of_birth DATE,
    course_id INT,
    year_of_study INT,
    room INT,
    registration_date DATE,
    address TEXT,
    guardian_name VARCHAR(100),
    guardian_contact VARCHAR(15),
    FOREIGN KEY (course_id) REFERENCES courses(id)
);

-- 📚 Create courses table
CREATE TABLE courses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    course_name VARCHAR(100) NOT NULL
);

-- 🏠 Create rooms table
CREATE TABLE rooms (
    id INT AUTO_INCREMENT PRIMARY KEY,
    room_number VARCHAR(10) NOT NULL UNIQUE,
    capacity INT NOT NULL,
    current_occupancy INT DEFAULT 0
);

-- 🚨 Create complaints table
CREATE TABLE complaints (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    complaint_text TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'need to fix',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- 💬 Create feedback table
CREATE TABLE feedback (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    feedback_text TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- 🏖️ Create leave_requests table
CREATE TABLE leave_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    leave_from DATE NOT NULL,
    leave_to DATE NOT NULL,
    reason TEXT NOT NULL,
    status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    approved_at TIMESTAMP NULL,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- 📅 Create attendance table
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    date DATE NOT NULL,
    status VARCHAR(20) NOT NULL,
    UNIQUE(student_id, date),
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- 🔐 Insert default admin (username: admin, password: admin)
INSERT INTO admin (username, password) VALUES ('admin', '$2y$10$yourhashedpasswordhere');
-- Note: Replace the password with a hashed version of 'admin' using PHP's password_hash() function.
-- Example: echo password_hash('admin', PASSWORD_DEFAULT);
```

**🔗 Update Database Connection:**
Open `includes/dbconn.php` and ensure it matches your database credentials (default for XAMPP is shown below):

```php
<?php
$host = 'localhost';
$username = 'root';
$password = ''; // Default XAMPP password is empty
$dbname = 'hostel_management';

$mysqli = new mysqli($host, $username, $password, $dbname);

if ($mysqli->connect_error) {
    die('Connection failed: ' . $mysqli->connect_error);
}
?>
```

### 3️⃣ Start the Application

1. ✅ Ensure Apache and MySQL are running in XAMPP
2. 🌐 Open a web browser and go to http://localhost/Hostel-Management-System/
3. 👀 You should see the landing page (index.php) with options to log in as an admin or user

---

## 📖 Usage Guide

### 🔐 Admin Access

**🚪 Login:**
- 🌐 Go to http://localhost/Hostel-Management-System/admin/adminpage.php
- 🔑 Use the default credentials:
  - **Username:** `admin`
  - **Password:** `admin`
  
> ⚠️ *(Change the password in the database after the first login for security.)*

**📊 Dashboard:**
- 📈 View statistics (total students, rooms, courses, complaints, feedback)
- 🧭 Navigate to manage students, courses, rooms, complaints, feedback, leave requests, and attendance

**📅 View All Attendance:**
- 🖱️ Click "View All Attendance" in the sidebar
- 📆 Select a date to view the attendance status of all students for that date

### 👨‍🎓 User (Student) Access

**🚪 Login:**
- 🌐 Go to http://localhost/Hostel-Management-System/userlogin.php
- 👤 Students must be registered by the admin in the students table with a password

**🏠 Dashboard:**
- 📄 View personal information
- 🔧 Register complaints, submit feedback, request leaves, view attendance, and mark attendance

**✅ Mark Attendance:**
- 🖱️ Click "Mark Attendance" in the sidebar
- 📅 Select a date and mark your status (Present/Absent)



---

## 🐛 Troubleshooting

| ❌ Issue | 🔍 Cause | ✅ Solution |
|----------|----------|-------------|
| **Database Connection Error** | MySQL not running | Ensure MySQL is running in XAMPP |
| **Styles Not Loading** | CSS file issues | Ensure css/style.css exists and is accessible |
| **Login Issues** | Incorrect credentials | Verify admin password in database |

**🔧 Additional Steps:**
- 🖥️ Verify the database credentials in `includes/dbconn.php`
- 🌐 Check the browser console for errors (right-click > Inspect > Console)
- 🔐 Ensure student records exist in the students table for user login



Notes

The application uses Bootstrap 3.3.7 for styling, so ensure an internet connection for CDN links or host Bootstrap locally.
Passwords in the database should be hashed using PHP’s password_hash() function for security.
The project can be extended with features like pagination, email notifications, or advanced reporting.

<?php
session_start();
include('../includes/dbconn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id'])) {
    echo "<script>alert('Please log in to access the dashboard.'); window.location.href='../userlogin.php';</script>";
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch student information
$stmt = $mysqli->prepare("SELECT s.*, c.course_name 
                         FROM students s 
                         LEFT JOIN courses c ON s.course_id = c.id 
                         WHERE s.id = ?");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$student = $result->fetch_assoc();
$stmt->close();

if (!$student) {
    echo "<script>alert('Student information not found.'); window.location.href='../userlogin.php';</script>";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Dashboard - Hostel Management System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="../css/style.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            padding-top: 20px;
            position: fixed;
            height: 100%;
        }
        .sidebar h2 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar a {
            padding: 15px 20px;
            display: block;
            color: white;
            text-decoration: none;
            font-size: 16px;
        }
        .sidebar a:hover {
            background-color: #34495e;
        }
        .content {
            margin-left: 250px;
            padding: 40px;
            flex: 1;
        }
        .content h2 {
            margin-bottom: 30px;
            color: #333;
        }
        .info-card {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 800px;
        }
        .info-card h3 {
            margin-bottom: 20px;
            color: #333;
        }
        .info-item {
            margin-bottom: 15px;
        }
        .info-item label {
            font-weight: bold;
            color: #555;
            display: inline-block;
            width: 200px;
        }
        .info-item span {
            color: #333;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Hostel Management System</h2>
        <a href="dashboard.php">Dashboard</a>
        <a href="register-complaint.php">Register Complaint</a>
        <a href="feedback.php">Feedback</a>
        <a href="leave-request.php">Leave Request</a>
        <a href="view-attendance.php">View Attendance</a>
        <a href="../userlogin.php">Logout</a>
    </div>
    <div class="content">
        <h2>User Dashboard</h2>
        <div class="info-card">
            <h3>Your Information</h3>
            <div class="info-item">
                <label>Full Name:</label>
                <span><?php echo htmlspecialchars($student['name']); ?></span>
            </div>
            <div class="info-item">
                <label>Email:</label>
                <span><?php echo htmlspecialchars($student['email']); ?></span>
            </div>
            <div class="info-item">
                <label>Contact Number:</label>
                <span><?php echo htmlspecialchars($student['contact_number']); ?></span>
            </div>
            <div class="info-item">
                <label>Emergency Contact:</label>
                <span><?php echo htmlspecialchars($student['emergency_contact']); ?></span>
            </div>
            <div class="info-item">
                <label>Gender:</label>
                <span><?php echo htmlspecialchars($student['gender']); ?></span>
            </div>
            <div class="info-item">
                <label>Date of Birth:</label>
                <span><?php echo htmlspecialchars($student['date_of_birth']); ?></span>
            </div>
            <div class="info-item">
                <label>Course:</label>
                <span><?php echo htmlspecialchars($student['course_name']); ?></span>
            </div>
            <div class="info-item">
                <label>Year of Study:</label>
                <span><?php echo htmlspecialchars($student['year_of_study']); ?></span>
            </div>
            <div class="info-item">
                <label>Room Number:</label>
                <span><?php echo htmlspecialchars($student['room']); ?></span>
            </div>
            <div class="info-item">
                <label>Registration Date:</label>
                <span><?php echo htmlspecialchars($student['registration_date']); ?></span>
            </div>
            <div class="info-item">
                <label>Address:</label>
                <span><?php echo htmlspecialchars($student['address']); ?></span>
            </div>
            <div class="info-item">
                <label>Guardian Name:</label>
                <span><?php echo htmlspecialchars($student['guardian_name']); ?></span>
            </div>
            <div class="info-item">
                <label>Guardian Contact:</label>
                <span><?php echo htmlspecialchars($student['guardian_contact']); ?></span>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
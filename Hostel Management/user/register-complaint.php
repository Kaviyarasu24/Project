<?php
session_start();
include('../includes/dbconn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id'])) {
    echo "<script>alert('Please log in to register a complaint.'); window.location.href='../userlogin.php';</script>";
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $complaint_type = $mysqli->real_escape_string($_POST['complaint_type']);
    $description = $mysqli->real_escape_string($_POST['description']);

    // Insert the complaint into the complaints table
    $stmt = $mysqli->prepare("INSERT INTO complaints (student_id, complaint_type, description, status) VALUES (?, ?, ?, 'need_to_fix')");
    $stmt->bind_param('iss', $student_id, $complaint_type, $description);

    if ($stmt->execute()) {
        echo "<script>alert('Complaint registered successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error registering complaint: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register Complaint - Hostel Management System</title>
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
        .complaint-form {
            max-width: 600px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            color: #555;
            display: block;
            margin-bottom: 8px;
        }
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 150px;
        }
        .btn-submit {
            width: 100%;
            padding: 12px;
            background-color: #5cb85c;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background-color: #4cae4c;
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
        <h2>Register a Complaint</h2>
        <div class="complaint-form">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="complaint_type">Complaint Type *</label>
                    <select id="complaint_type" name="complaint_type" required>
                        <option value="">Select Complaint Type</option>
                        <option value="Maintenance">Maintenance</option>
                        <option value="Food">Food</option>
                        <option value="Room">Room</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="description">Complaint Description *</label>
                    <textarea id="description" name="description" required placeholder="Describe your complaint here..."></textarea>
                </div>
                <button type="submit" class="btn-submit">Submit Complaint</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
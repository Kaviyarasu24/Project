<?php
session_start();
include('../includes/dbconn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id'])) {
    echo "<script>alert('Please log in to submit feedback.'); window.location.href='../userlogin.php';</script>";
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $accessibility = $mysqli->real_escape_string($_POST['accessibility']);
    $mess = $mysqli->real_escape_string($_POST['mess']);

    // Insert the feedback into the feedback table
    $stmt = $mysqli->prepare("INSERT INTO feedback (student_id, accessibility, mess) VALUES (?, ?, ?)");
    $stmt->bind_param('iss', $student_id, $accessibility, $mess);

    if ($stmt->execute()) {
        echo "<script>alert('Feedback submitted successfully!'); window.location.href='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error submitting feedback: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit Feedback - Hostel Management System</title>
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
        .feedback-form {
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
        .form-group select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
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
        <h2>Submit Feedback</h2>
        <div class="feedback-form">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="accessibility">Accessibility Rating *</label>
                    <select id="accessibility" name="accessibility" required>
                        <option value="">Select Rating</option>
                        <option value="Poor">Poor</option>
                        <option value="Average">Average</option>
                        <option value="Good">Good</option>
                        <option value="Very Good">Very Good</option>
                        <option value="Excellent">Excellent</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="mess">Mess Rating *</label>
                    <select id="mess" name="mess" required>
                        <option value="">Select Rating</option>
                        <option value="Poor">Poor</option>
                        <option value="Average">Average</option>
                        <option value="Good">Good</option>
                        <option value="Very Good">Very Good</option>
                        <option value="Excellent">Excellent</option>
                    </select>
                </div>
                <button type="submit" class="btn-submit">Submit Feedback</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
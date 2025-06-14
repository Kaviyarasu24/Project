<?php
session_start();
include('../includes/dbconn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id'])) {
    echo "<script>alert('Please log in to submit a leave request.'); window.location.href='../userlogin.php';</script>";
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leave_from = $mysqli->real_escape_string($_POST['leave_from']);
    $leave_to = $mysqli->real_escape_string($_POST['leave_to']);
    $reason = $mysqli->real_escape_string($_POST['reason']);

    // Validate dates
    if (strtotime($leave_to) < strtotime($leave_from)) {
        echo "<script>alert('End date cannot be earlier than start date.');</script>";
    } else {
        // Insert the leave request into the leave_requests table
        $stmt = $mysqli->prepare("INSERT INTO leave_requests (student_id, leave_from, leave_to, reason, status) VALUES (?, ?, ?, ?, 'Pending')");
        $stmt->bind_param('isss', $student_id, $leave_from, $leave_to, $reason);

        if ($stmt->execute()) {
            echo "<script>alert('Leave request submitted successfully!'); window.location.href='dashboard.php';</script>";
        } else {
            echo "<script>alert('Error submitting leave request: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Submit Leave Request - Hostel Management System</title>
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
        .leave-form {
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
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
            min-height: 100px;
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
        <h2>Submit Leave Request</h2>
        <div class="leave-form">
            <form method="POST" action="">
                <div class="form-group">
                    <label for="leave_from">Leave From *</label>
                    <input type="date" id="leave_from" name="leave_from" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="leave_to">Leave To *</label>
                    <input type="date" id="leave_to" name="leave_to" required min="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="form-group">
                    <label for="reason">Reason for Leave *</label>
                    <textarea id="reason" name="reason" required placeholder="Describe the reason for your leave..."></textarea>
                </div>
                <button type="submit" class="btn-submit">Submit Leave Request</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
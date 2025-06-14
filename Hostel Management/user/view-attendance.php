<?php
session_start();
include('../includes/dbconn.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['student_id'])) {
    echo "<script>alert('Please log in to view attendance.'); window.location.href='../userlogin.php';</script>";
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch attendance records
$stmt = $mysqli->prepare("SELECT date, status FROM attendance WHERE student_id = ? ORDER BY date DESC");
$stmt->bind_param('i', $student_id);
$stmt->execute();
$result = $stmt->get_result();
$attendance_records = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Attendance - Hostel Management System</title>
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
        .attendance-table {
            max-width: 600px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .attendance-table table {
            width: 100%;
            border-collapse: collapse;
        }
        .attendance-table th,
        .attendance-table td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .attendance-table th {
            background-color: #f8f8f8;
            color: #333;
        }
        .attendance-table td {
            color: #555;
        }
        .attendance-table .status-present {
            color: #5cb85c;
            font-weight: bold;
        }
        .attendance-table .status-absent {
            color: #d9534f;
            font-weight: bold;
        }
        .attendance-table .status-on-leave {
            color: #f0ad4e;
            font-weight: bold;
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
        <a href="logout.php">Logout</a>
    </div>
    <div class="content">
        <h2>Your Attendance Records</h2>
        <div class="attendance-table">
            <?php if (empty($attendance_records)): ?>
                <p>No attendance records found.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendance_records as $record): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($record['date']); ?></td>
                                <td class="status-<?php echo strtolower(str_replace(' ', '-', $record['status'])); ?>">
                                    <?php echo htmlspecialchars($record['status']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
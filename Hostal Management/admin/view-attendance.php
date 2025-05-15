<?php
session_start();
include('../includes/dbconn.php');

// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
    header("Location: adminpage.php");
    exit();
}

$date = date('Y-m-d'); // Default to today
if (isset($_GET['date'])) {
    $date = $mysqli->real_escape_string($_GET['date']);
}

// Fetch all students
$students_query = "SELECT id, name FROM students ORDER BY name";
$students_result = $mysqli->query($students_query);
$students = $students_result->fetch_all(MYSQLI_ASSOC);

// Fetch attendance for the selected date
$attendance_query = "SELECT student_id, status FROM attendance WHERE date = ?";
$stmt = $mysqli->prepare($attendance_query);
$stmt->bind_param('s', $date);
$stmt->execute();
$attendance_result = $stmt->get_result();
$attendance_data = [];
while ($row = $attendance_result->fetch_assoc()) {
    $attendance_data[$row['student_id']] = $row['status'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View All Attendance - Hostel Management System</title>
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
        
        .content {
            margin-left: 250px;
            padding: 20px;
            flex: 1;
        }
        .content h2 {
            margin-bottom: 30px;
            color: #333;
        }
        .panel-body table {
            width: 100%;
            border-collapse: collapse;
        }
        .panel-body th,
        .panel-body td {
            padding: 10px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        .panel-body th {
            background-color: #f8f8f8;
            color: #333;
        }
        .panel-body td {
            color: #555;
        }
        .panel-body .status-present {
            color: #5cb85c;
            font-weight: bold;
        }
        .panel-body .status-absent {
            color: #d9534f;
            font-weight: bold;
        }
        .panel-body .status-on-leave {
            color: #f0ad4e;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Hostel Management System</h2>
        <a href="dashboard.php">Dashboard</a>
        <div class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Courses <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="add-course.php">Add Course</a></li>
                <li><a href="manage-courses.php">Manage Courses</a></li>
            </ul>
        </div>
        <div class="dropdown">
            <a class="dropdown-toggle" data-toggle="dropdown" href="#">Rooms <span class="caret"></span></a>
            <ul class="dropdown-menu">
                <li><a href="add-room.php">Add Room</a></li>
                <li><a href="manage-rooms.php">Manage Rooms</a></li>
            </ul>
        </div>
        <a href="manage-students.php">Manage Students</a>
        <a href="complaints.php">Complaints</a>
        <a href="view-attendance.php">View Attendance</a>
        <a href="leave-approval.php">Leave Approval</a>
        <a href="mark-attendance.php">Mark Attendance</a>
        <a href="feedback.php">Feedback</a>
        <a href="../index.php">Logout</a>
    </div>
    <div class="content">
        <h2>View All Attendance</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body bk-info text-light">
                        <div class="stat-panel">
                            <form method="GET" action="" style="margin-bottom: 20px;">
                                <div class="form-group">
                                    <label for="date">Select Date:</label>
                                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required max="<?php echo date('Y-m-d'); ?>">
                                    <button type="submit" class="btn btn-primary" style="margin-left: 10px;">View</button>
                                </div>
                            </form>
                            <?php if (empty($students)): ?>
                                <p>No students found.</p>
                            <?php else: ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($students as $student): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($student['name']); ?></td>
                                                <td class="status-<?php echo strtolower(str_replace(' ', '-', ($attendance_data[$student['id']] ?? 'Not Marked'))); ?>">
                                                    <?php echo htmlspecialchars($attendance_data[$student['id']] ?? 'Not Marked'); ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="panel-footer text-center">
                        <span class="block-anchor">Attendance Overview for <?php echo htmlspecialchars($date); ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
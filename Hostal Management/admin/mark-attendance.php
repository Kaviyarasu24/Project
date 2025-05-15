<?php
session_start();
include('../includes/dbconn.php');

// Check if the admin is logged in


$date = date('Y-m-d'); // Default to today
if (isset($_GET['date'])) {
    $date = $mysqli->real_escape_string($_GET['date']);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $date = $mysqli->real_escape_string($_POST['date']);
    $attendance_data = $_POST['attendance'] ?? [];

    foreach ($attendance_data as $student_id => $status) {
        $student_id = (int)$student_id;
        $status = $mysqli->real_escape_string($status);
        // Insert or update attendance record
        $stmt = $mysqli->prepare("INSERT INTO attendance (student_id, date, status) 
                                 VALUES (?, ?, ?) 
                                 ON DUPLICATE KEY UPDATE status = ?");
        $stmt->bind_param('isss', $student_id, $date, $status, $status);
        $stmt->execute();
        $stmt->close();
    }
    echo "<script>alert('Attendance marked successfully!'); window.location.href='mark-attendance.php?date=$date';</script>";
}

// Fetch all students
$students_query = "SELECT id, name FROM students ORDER BY name";
$students_result = $mysqli->query($students_query);
$students = $students_result->fetch_all(MYSQLI_ASSOC);

// Fetch existing attendance for the selected date
$attendance_query = "SELECT student_id, status FROM attendance WHERE date = ? AND student_id IN (SELECT id FROM students)";
$stmt = $mysqli->prepare($attendance_query);
$stmt->bind_param('s', $date);
$stmt->execute();
$attendance_result = $stmt->get_result();
$existing_attendance = [];
while ($row = $attendance_result->fetch_assoc()) {
    $existing_attendance[$row['student_id']] = $row['status'];
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Mark Attendance - Hostel Management System</title>
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
            padding: 40px;
            flex: 1;
        }
        .content h2 {
            margin-bottom: 30px;
            color: #333;
        }
        .attendance-form {
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
        .form-group input {
            width: 200px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
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
        .attendance-table select {
            padding: 5px;
            border: 1px solid #ddd;
            border-radius: 4px;
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
            margin-top: 20px;
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
        <h2>Mark Attendance</h2>
        <div class="attendance-form">
            <form method="GET" action="">
                <div class="form-group">
                    <label for="date">Select Date</label>
                    <input type="date" id="date" name="date" value="<?php echo htmlspecialchars($date); ?>" required max="<?php echo date('Y-m-d'); ?>">
                    <button type="submit" class="btn btn-primary" style="padding: 10px 20px;">View</button>
                </div>
            </form>
            <form method="POST" action="">
                <input type="hidden" name="date" value="<?php echo htmlspecialchars($date); ?>">
                <div class="attendance-table">
                    <table>
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
                                    <td>
                                        <select name="attendance[<?php echo $student['id']; ?>]" required>
                                            <option value="Present" <?php echo (isset($existing_attendance[$student['id']]) && $existing_attendance[$student['id']] == 'Present') ? 'selected' : ''; ?>>Present</option>
                                            <option value="Absent" <?php echo (isset($existing_attendance[$student['id']]) && $existing_attendance[$student['id']] == 'Absent') ? 'selected' : ''; ?>>Absent</option>
                                            <option value="On Leave" <?php echo (isset($existing_attendance[$student['id']]) && $existing_attendance[$student['id']] == 'On Leave') ? 'selected' : ''; ?>>On Leave</option>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <button type="submit" class="btn-submit">Mark Attendance</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
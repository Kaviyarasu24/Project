<?php
session_start();
include('../includes/dbconn.php');
if (!isset($_SESSION['admin'])) {
    header("Location: adminpage.php");
    exit();
}

// Fetch feedback entries with student details
$result = $mysqli->query("SELECT f.id, f.student_id, f.accessibility, f.mess, 
                          s.name AS student_name, s.room, c.course_name 
                          FROM feedback f 
                          LEFT JOIN students s ON f.student_id = s.id 
                          LEFT JOIN courses c ON s.course_id = c.id 
                          ORDER BY f.id DESC");
$feedback_count = $result->num_rows;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>View Feedback</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .content {
            padding: 30px;
        }
        .table-container {
            overflow-x: auto;
        }
        table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        th, td {
            padding: 12px 16px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        th:nth-child(1), td:nth-child(1) { /* Feedback ID */
            width: 10%;
        }
        th:nth-child(2), td:nth-child(2) { /* Student Name */
            width: 15%;
        }
        th:nth-child(3), td:nth-child(3) { /* Room */
            width: 10%;
        }
        th:nth-child(4), td:nth-child(4) { /* Course */
            width: 15%;
        }
        th:nth-child(5), td:nth-child(5) { /* Accessibility Feedback */
            width: 25%;
        }
        th:nth-child(6), td:nth-child(6) { /* Mess Feedback */
            width: 25%;
        }
        .no-data {
            text-align: center;
            padding: 20px;
            color: #777;
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
        <h2>View Feedback</h2>
        <div class="table-container">
            <?php if ($feedback_count > 0) { ?>
                <table>
                    <tr>
                        <th>Feedback ID</th>
                        <th>Student Name</th>
                        <th>Room</th>
                        <th>Course</th>
                        <th>Accessibility Feedback</th>
                        <th>Mess Feedback</th>
                    </tr>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['student_name'] ?? 'Unknown Student'); ?></td>
                        <td><?php echo htmlspecialchars($row['room'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['course_name'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['accessibility'] ?? 'N/A'); ?></td>
                        <td><?php echo htmlspecialchars($row['mess'] ?? 'N/A'); ?></td>
                    </tr>
                    <?php } ?>
                </table>
            <?php } else { ?>
                <p class="no-data">No feedback available at this time.</p>
            <?php } ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
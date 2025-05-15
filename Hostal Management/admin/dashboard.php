<?php
session_start();
include('../includes/dbconn.php');
if (!isset($_SESSION['admin'])) {
    header("Location: adminpage.php");
    exit();
}

// Fetch statistics
$total_students = $mysqli->query("SELECT COUNT(*) FROM students")->fetch_row()[0];
$total_rooms = $mysqli->query("SELECT COUNT(*) FROM rooms")->fetch_row()[0];
$total_courses = $mysqli->query("SELECT COUNT(*) FROM courses")->fetch_row()[0];
$need_to_fix_complaints = $mysqli->query("SELECT COUNT(*) FROM complaints WHERE status = 'need to fix'")->fetch_row()[0];
$total_feedback = $mysqli->query("SELECT COUNT(*) FROM feedback")->fetch_row()[0];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
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
        <h2>Dashboard</h2>
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body bk-primary text-light">
                        <div class="stat-panel text-center">
                            <div class="stat-panel-number h1"><?php echo $total_students; ?></div>
                            <div class="stat-panel-title text-uppercase">Total Students</div>
                        </div>
                    </div>
                    <a href="manage-students.php" class="block-anchor panel-footer">Full Detail <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body bk-success text-light">
                        <div class="stat-panel text-center">
                            <div class="stat-panel-number h1"><?php echo $total_rooms; ?></div>
                            <div class="stat-panel-title text-uppercase">Total Rooms</div>
                        </div>
                    </div>
                    <a href="manage-rooms.php" class="block-anchor panel-footer text-center">See All <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body bk-info text-light">
                        <div class="stat-panel text-center">
                            <div class="stat-panel-number h1"><?php echo $total_courses; ?></div>
                            <div class="stat-panel-title text-uppercase">Total Courses</div>
                        </div>
                    </div>
                    <a href="manage-courses.php" class="block-anchor panel-footer text-center">See All <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body bk-info text-light">
                        <div class="stat-panel text-center">
                            <div class="stat-panel-number h1"><?php echo $need_to_fix_complaints; ?></div>
                            <div class="stat-panel-title text-uppercase">Need to Fix Complaints</div>
                        </div>
                    </div>
                    <a href="complaints.php" class="block-anchor panel-footer">Full Detail <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-body bk-info text-light">
                        <div class="stat-panel text-center">
                            <div class="stat-panel-number h1"><?php echo $total_feedback; ?></div>
                            <div class="stat-panel-title text-uppercase">Total Feedbacks</div>
                        </div>
                    </div>
                    <a href="feedback.php" class="block-anchor panel-footer text-center">See All <i class="fa fa-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
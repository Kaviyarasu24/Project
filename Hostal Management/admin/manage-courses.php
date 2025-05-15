<?php
session_start();
include('../includes/dbconn.php');
if (!isset($_SESSION['admin'])) {
    header("Location: adminpage.php");
    exit();
}

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    // Check if the course is assigned to any students
    $check_stmt = $mysqli->prepare("SELECT COUNT(*) FROM students WHERE course_id = ?");
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    $check_stmt->bind_result($student_count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($student_count > 0) {
        echo "<script>alert('Cannot delete course. It is assigned to $student_count student(s).');</script>";
    } else {
        $stmt = $mysqli->prepare("DELETE FROM courses WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo "<script>alert('Course deleted successfully');</script>";
        } else {
            echo "<script>alert('Error deleting course: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
}

// Handle Edit
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $course_name = $_POST['course_name'];
    // Check if the course name already exists (excluding the current course)
    $check_stmt = $mysqli->prepare("SELECT id FROM courses WHERE course_name = ? AND id != ?");
    $check_stmt->bind_param('si', $course_name, $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Course name already exists. Please use a different name.');</script>";
    } else {
        $stmt = $mysqli->prepare("UPDATE courses SET course_name = ? WHERE id = ?");
        $stmt->bind_param('si', $course_name, $id);
        if ($stmt->execute()) {
            echo "<script>alert('Course updated successfully');</script>";
        } else {
            echo "<script>alert('Error updating course: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
    $check_stmt->close();
}

$result = $mysqli->query("SELECT * FROM courses");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Courses</title>
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
            min-width: 600px;
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
        th:nth-child(1), td:nth-child(1) { /* ID */
            width: 20%;
        }
        th:nth-child(2), td:nth-child(2) { /* Course Name */
            width: 50%;
        }
        th:nth-child(3), td:nth-child(3) { /* Actions */
            width: 30%;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            display: block;
        }
        .btn-sm {
            padding: 5px 10px;
        }
        .btn-success {
            background-color: #5cb85c;
        }
        .btn-success:hover {
            background-color: #4cae4c;
        }
        .btn-danger {
            background-color: #d9534f;
        }
        .btn-danger:hover {
            background-color: #c9302c;
        }
        .action-buttons {
            display: flex;
            gap: 10px;
            align-items: center;
            flex-wrap: nowrap;
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
        <h2>Manage Courses</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Course Name</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="course_name" value="<?php echo htmlspecialchars($row['course_name']); ?>" required>
                    </td>
                    <td>
                            <div class="action-buttons">
                                <button type="submit" name="update" class="btn btn-success btn-sm">Update</button>
                            </form>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
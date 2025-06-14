<?php
session_start();
include('../includes/dbconn.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $request_id = $mysqli->real_escape_string($_POST['request_id']);
    $action = $mysqli->real_escape_string($_POST['action']);

    $status = ($action == 'approve') ? 'Approved' : 'Rejected';
    $stmt = $mysqli->prepare("UPDATE leave_requests SET status = ?, approved_at = CURRENT_TIMESTAMP WHERE id = ?");
    $stmt->bind_param('si', $status, $request_id);

    if ($stmt->execute()) {
        // If approved, mark the student as "On Leave" in the attendance table for the leave dates
        if ($action == 'approve') {
            $stmt2 = $mysqli->prepare("SELECT student_id, leave_from, leave_to FROM leave_requests WHERE id = ?");
            $stmt2->bind_param('i', $request_id);
            $stmt2->execute();
            $result2 = $stmt2->get_result();
            $leave = $result2->fetch_assoc();
            $stmt2->close();

            $student_id = $leave['student_id'];
            $start_date = new DateTime($leave['leave_from']);
            $end_date = new DateTime($leave['leave_to']);
            $interval = new DateInterval('P1D');
            $date_range = new DatePeriod($start_date, $interval, $end_date->modify('+1 day'));

            foreach ($date_range as $date) {
                $current_date = $date->format('Y-m-d');
                // Insert or update attendance record
                $stmt3 = $mysqli->prepare("INSERT INTO attendance (student_id, date, status) 
                                          VALUES (?, ?, 'On Leave') 
                                          ON DUPLICATE KEY UPDATE status = 'On Leave'");
                $stmt3->bind_param('is', $student_id, $current_date);
                $stmt3->execute();
                $stmt3->close();
            }
        }
        echo "<script>alert('Leave request has been $status successfully!'); window.location.href='leave-approval.php';</script>";
    } else {
        echo "<script>alert('Error updating leave request: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
}

// Fetch all leave requests with student details
$query = "SELECT lr.id, lr.student_id, lr.leave_from, lr.leave_to, lr.reason, lr.status, s.name 
          FROM leave_requests lr 
          JOIN students s ON lr.student_id = s.id 
          ORDER BY lr.id DESC";
$result = $mysqli->query($query);
$leave_requests = $result->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Leave Approval - Hostel Management System</title>
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
            padding: 20px; /* Reduced padding to match dashboard */
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
        .panel-body .status-pending {
            color: #f0ad4e;
            font-weight: bold;
        }
        .panel-body .status-approved {
            color: #5cb85c;
            font-weight: bold;
        }
        .panel-body .status-rejected {
            color: #d9534f;
            font-weight: bold;
        }
        .btn-approve {
            background-color: #5cb85c;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-approve:hover {
            background-color: #4cae4c;
        }
        .btn-reject {
            background-color: #d9534f;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-reject:hover {
            background-color: #c9302c;
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
        <h2>Leave Approval</h2>
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-body bk-info text-light">
                        <div class="stat-panel">
                            <?php if (empty($leave_requests)): ?>
                                <p>No leave requests found.</p>
                            <?php else: ?>
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Student Name</th>
                                            <th>Leave From</th>
                                            <th>Leave To</th>
                                            <th>Reason</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($leave_requests as $request): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($request['name']); ?></td>
                                                <td><?php echo htmlspecialchars($request['leave_from']); ?></td>
                                                <td><?php echo htmlspecialchars($request['leave_to']); ?></td>
                                                <td><?php echo htmlspecialchars($request['reason']); ?></td>
                                                <td class="status-<?php echo strtolower($request['status']); ?>">
                                                    <?php echo htmlspecialchars($request['status']); ?>
                                                </td>
                                                <td>
                                                    <?php if ($request['status'] == 'Pending'): ?>
                                                        <form method="POST" action="" style="display:inline;">
                                                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                            <input type="hidden" name="action" value="approve">
                                                            <button type="submit" class="btn-approve">Approve</button>
                                                        </form>
                                                        <form method="POST" action="" style="display:inline;">
                                                            <input type="hidden" name="request_id" value="<?php echo $request['id']; ?>">
                                                            <input type="hidden" name="action" value="reject">
                                                            <button type="submit" class="btn-reject">Reject</button>
                                                        </form>
                                                    <?php else: ?>
                                                        -
                                                    <?php endif; ?>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            <?php endif; ?>
                        </div>
                    </div>
                    <div class="panel-footer text-center">
                        <span class="block-anchor">Leave Requests Overview</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
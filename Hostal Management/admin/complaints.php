<?php
session_start();
include('../includes/dbconn.php');
if (!isset($_SESSION['admin'])) {
    header("Location: adminpage.php");
    exit();
}

// Handle status update
if (isset($_GET['update_status'])) {
    $complaint_id = $_GET['update_status'];
    $new_status = $_GET['new_status'];

    // Update the complaint status
    $stmt = $mysqli->prepare("UPDATE complaints SET status = ? WHERE id = ?");
    $stmt->bind_param('si', $new_status, $complaint_id);
    if ($stmt->execute()) {
        // Log the status change in complainthistory
        $stmt2 = $mysqli->prepare("INSERT INTO complainthistory (complaint_id, status, updated_at) VALUES (?, ?, NOW())");
        $stmt2->bind_param('is', $complaint_id, $new_status);
        $stmt2->execute();
        $stmt2->close();
        echo "<script>alert('Status updated successfully');</script>";
    } else {
        echo "<script>alert('Error updating status: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
}

// Fetch complaints with student details
$result = $mysqli->query("SELECT c.id, c.description, c.status, s.name, s.room 
                         FROM complaints c 
                         JOIN students s ON c.student_id = s.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Complaints</title>
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
            min-width: 800px;
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
        th:nth-child(1), td:nth-child(1) { /* S.No */
            width: 10%;
        }
        th:nth-child(2), td:nth-child(2) { /* Name */
            width: 20%;
        }
        th:nth-child(3), td:nth-child(3) { /* Room No */
            width: 15%;
        }
        th:nth-child(4), td:nth-child(4) { /* Complaint Type */
            width: 20%;
        }
        th:nth-child(5), td:nth-child(5) { /* Read Complaint */
            width: 15%;
        }
        th:nth-child(6), td:nth-child(6) { /* Action */
            width: 20%;
        }
        .btn-sm {
            padding: 5px 10px;
        }
        .btn-info {
            background-color: #5bc0de;
        }
        .btn-info:hover {
            background-color: #31b0d5;
        }
        .btn-warning {
            background-color: #f0ad4e;
        }
        .btn-warning:hover {
            background-color: #ec971f;
        }
        .btn-success {
            background-color: #5cb85c;
        }
        .btn-success:hover {
            background-color: #4cae4c;
        }
        .modal-content {
            border-radius: 8px;
        }
        .modal-header {
            background-color: #f2f2f2;
            border-bottom: 1px solid #ddd;
        }
        .modal-body {
            padding: 20px;
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
        <h2>Manage Complaints</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>S.No</th>
                    <th>Name</th>
                    <th>Room No</th>
                    <th>Complaint Type</th>
                    <th>Read Complaint</th>
                    <th>Action</th>
                </tr>
                <?php 
                $serial_no = 1;
                while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $serial_no++; ?></td>
                    <td><?php echo htmlspecialchars($row['name']); ?></td>
                    <td><?php echo htmlspecialchars($row['room']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                    <td>
                        <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#complaintModal<?php echo $row['id']; ?>">Read Complaint</button>
                    </td>
                    <td>
                        <?php if ($row['status'] == 'need to fix') { ?>
                            <a href="?update_status=<?php echo $row['id']; ?>&new_status=fixed" class="btn btn-success btn-sm">Mark as Fixed</a>
                        <?php } else { ?>
                            <a href="?update_status=<?php echo $row['id']; ?>&new_status=need to fix" class="btn btn-warning btn-sm">Mark as Need to Fix</a>
                        <?php } ?>
                    </td>
                </tr>
                <!-- Modal for complaint details -->
                <div class="modal fade" id="complaintModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="complaintModalLabel<?php echo $row['id']; ?>">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title" id="complaintModalLabel<?php echo $row['id']; ?>">Complaint Details</h4>
                            </div>
                            <div class="modal-body">
                                <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php } ?>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
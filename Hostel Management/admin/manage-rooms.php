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
    // Check if the room is assigned to any students
    $check_stmt = $mysqli->prepare("SELECT COUNT(*) FROM students WHERE room = (SELECT room_number FROM rooms WHERE id = ?)");
    $check_stmt->bind_param('i', $id);
    $check_stmt->execute();
    $check_stmt->bind_result($student_count);
    $check_stmt->fetch();
    $check_stmt->close();

    if ($student_count > 0) {
        echo "<script>alert('Cannot delete room. It is assigned to $student_count student(s).');</script>";
    } else {
        $stmt = $mysqli->prepare("DELETE FROM rooms WHERE id = ?");
        $stmt->bind_param('i', $id);
        if ($stmt->execute()) {
            echo "<script>alert('Room deleted successfully');</script>";
        } else {
            echo "<script>alert('Error deleting room');</script>";
        }
        $stmt->close();
    }
}

// Handle Edit
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $room_number = $_POST['room_number'];
    $seater_option = $_POST['seater'];

    // Map seater option to fee
    $seater_fees = [
        '3 Roommate with AC (115000)' => 115000,
        '3 Roommate with NON-AC (95000)' => 95000,
        '4 Roommate with AC (105000)' => 105000,
        '4 Roommate with NON-AC (85000)' => 85000
    ];

    $fee = $seater_fees[$seater_option];

    // Check if the new room number already exists (excluding the current room)
    $check_stmt = $mysqli->prepare("SELECT id FROM rooms WHERE room_number = ? AND id != ?");
    $check_stmt->bind_param('si', $room_number, $id);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Room number already exists. Please use a different room number.');</script>";
    } else {
        $stmt = $mysqli->prepare("UPDATE rooms SET room_number = ?, seater = ?, fee = ? WHERE id = ?");
        $stmt->bind_param('ssdi', $room_number, $seater_option, $fee, $id);
        if ($stmt->execute()) {
            echo "<script>alert('Room updated successfully');</script>";
        } else {
            echo "<script>alert('Error updating room');</script>";
        }
        $stmt->close();
    }
    $check_stmt->close();
}

$result = $mysqli->query("SELECT * FROM rooms");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Rooms</title>
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
        th:nth-child(1), td:nth-child(1) { /* ID */
            width: 10%;
        }
        th:nth-child(2), td:nth-child(2) { /* Room Number */
            width: 20%;
        }
        th:nth-child(3), td:nth-child(3) { /* Seater Type */
            width: 30%;
        }
        th:nth-child(4), td:nth-child(4) { /* Fee */
            width: 20%;
        }
        th:nth-child(5), td:nth-child(5) { /* Actions */
            width: 20%;
        }
        input[type="text"],
        input[type="number"],
        select {
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
        <h2>Manage Rooms</h2>
        <div class="table-container">
            <table>
                <tr>
                    <th>ID</th>
                    <th>Room Number</th>
                    <th>Seater Type</th>
                    <th>Fee (INR)</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td>
                        <form method="POST" action="" style="display:inline;" class="room-form" data-id="<?php echo $row['id']; ?>">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="room_number" value="<?php echo htmlspecialchars($row['room_number']); ?>" required>
                    </td>
                    <td>
                            <select name="seater" class="seater-select" required>
                                <option value="3 Roommate with AC (115000)" <?php if ($row['seater'] == '3 Roommate with AC (115000)') echo 'selected'; ?>>3 Roommate with AC (115000)</option>
                                <option value="3 Roommate with NON-AC (95000)" <?php if ($row['seater'] == '3 Roommate with NON-AC (95000)') echo 'selected'; ?>>3 Roommate with NON-AC (95000)</option>
                                <option value="4 Roommate with AC (105000)" <?php if ($row['seater'] == '4 Roommate with AC (105000)') echo 'selected'; ?>>4 Roommate with AC (105000)</option>
                                <option value="4 Roommate with NON-AC (85000)" <?php if ($row['seater'] == '4 Roommate with NON-AC (85000)') echo 'selected'; ?>>4 Roommate with NON-AC (85000)</option>
                            </select>
                    </td>
                    <td>
                            <input type="number" name="fee_display" class="fee-display" value="<?php echo $row['fee']; ?>" readonly>
                    </td>
                    <td>
                            <div class="action-buttons">
                                <button type="submit" name="update" class="btn btn-success btn-sm">Update</button>
                            </form>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this room?');">Delete</a>
                        </div>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            const seaterFees = {
                '3 Roommate with AC (115000)': 115000,
                '3 Roommate with NON-AC (95000)': 95000,
                '4 Roommate with AC (105000)': 105000,
                '4 Roommate with NON-AC (85000)': 85000
            };

            $('.seater-select').on('change', function() {
                const selectedSeater = $(this).val();
                const fee = seaterFees[selectedSeater];
                $(this).closest('tr').find('.fee-display').val(fee);
            });
        });
    </script>
</body>
</html>
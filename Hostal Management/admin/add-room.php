<?php
session_start();
include('../includes/dbconn.php');
if (!isset($_SESSION['admin'])) {
    header("Location: adminpage.php");
    exit();
}

if (isset($_POST['submit'])) {
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

    // Check if room number already exists
    $check_stmt = $mysqli->prepare("SELECT room_number FROM rooms WHERE room_number = ?");
    $check_stmt->bind_param('s', $room_number);
    $check_stmt->execute();
    $check_stmt->store_result();

    if ($check_stmt->num_rows > 0) {
        echo "<script>alert('Room number already exists. Please use a different room number.');</script>";
    } else {
        // Insert the new room
        $stmt = $mysqli->prepare("INSERT INTO rooms (room_number, seater, fee) VALUES (?, ?, ?)");
        $stmt->bind_param('ssd', $room_number, $seater_option, $fee);
        if ($stmt->execute()) {
            echo "<script>alert('Room added successfully');</script>";
        } else {
            echo "<script>alert('Error adding room');</script>";
        }
        $stmt->close();
    }
    $check_stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Add Room</title>
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
        <h2>Add New Room</h2>
        <form method="POST" action="">
            <label>Room Number</label>
            <input type="text" name="room_number" required>
            <label>Select Seater</label>
            <select name="seater" id="seater" required>
                <option value="3 Roommate with AC (115000)">3 Roommate with AC (115000)</option>
                <option value="3 Roommate with NON-AC (95000)">3 Roommate with NON-AC (95000)</option>
                <option value="4 Roommate with AC (105000)">4 Roommate with AC (105000)</option>
                <option value="4 Roommate with NON-AC (85000)">4 Roommate with NON-AC (85000)</option>
            </select>
            <label>Fee per Student (INR)</label>
            <input type="number" name="fee_display" id="fee" readonly>
            <button type="submit" name="submit">Add Room</button>
        </form>
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

            function updateFee() {
                const selectedSeater = $('#seater').val();
                const fee = seaterFees[selectedSeater];
                $('#fee').val(fee);
            }

            // Update fee on page load for the default selection
            updateFee();

            // Update fee when seater selection changes
            $('#seater').on('change', updateFee);
        });
    </script>
</body>
</html>
<?php
session_start();
include('../includes/dbconn.php');
if (!isset($_SESSION['admin'])) {
    header("Location: adminpage.php");
    exit();
}

// Fetch courses and rooms for dropdowns
$courses_result = $mysqli->query("SELECT * FROM courses");
$rooms_result = $mysqli->query("SELECT room_number, seater FROM rooms");

// Fetch room seater limits for validation
$room_seater_limits = [];
while ($room = $rooms_result->fetch_assoc()) {
    preg_match('/(\d+)/', $room['seater'], $matches);
    $room_seater_limits[$room['room_number']] = (int)$matches[0];
}
$rooms_result->data_seek(0); // Reset pointer for the dropdown

// Handle Delete
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $stmt = $mysqli->prepare("DELETE FROM students WHERE id = ?");
    $stmt->bind_param('i', $id);
    if ($stmt->execute()) {
        echo "<script>alert('Student deleted successfully');</script>";
    } else {
        echo "<script>alert('Error deleting student: " . addslashes($stmt->error) . "');</script>";
    }
    $stmt->close();
}

// Handle Edit
if (isset($_POST['update'])) {
    $id = $_POST['id'];
    $name = $_POST['name'];
    $email = $_POST['email'];
    $contact_number = $_POST['contact_number'];
    $emergency_contact = $_POST['emergency_contact'];
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $course_id = $_POST['course_id'];
    $year_of_study = $_POST['year_of_study'];
    $room = $_POST['room'];
    $registration_date = $_POST['registration_date'];
    $address = $_POST['address'];
    $guardian_name = $_POST['guardian_name'];
    $guardian_contact = $_POST['guardian_contact'];

    // Check room availability (excluding the current student)
    $stmt = $mysqli->prepare("SELECT COUNT(*) FROM students WHERE room = ? AND id != ?");
    $stmt->bind_param('si', $room, $id);
    $stmt->execute();
    $stmt->bind_result($current_occupants);
    $stmt->fetch();
    $stmt->close();

    $seater_limit = $room_seater_limits[$room];
    if ($current_occupants >= $seater_limit) {
        echo "<script>alert('Selected room is already full. Please choose another room.');</script>";
    } else {
        // Update the student
        $stmt = $mysqli->prepare("UPDATE students SET name = ?, email = ?, contact_number = ?, emergency_contact = ?, gender = ?, date_of_birth = ?, course_id = ?, year_of_study = ?, room = ?, registration_date = ?, address = ?, guardian_name = ?, guardian_contact = ? WHERE id = ?");
        $stmt->bind_param('ssssssissssssi', $name, $email, $contact_number, $emergency_contact, $gender, $date_of_birth, $course_id, $year_of_study, $room, $registration_date, $address, $guardian_name, $guardian_contact, $id);
        if ($stmt->execute()) {
            echo "<script>alert('Student updated successfully');</script>";
        } else {
            echo "<script>alert('Error updating student: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
}

$result = $mysqli->query("SELECT s.*, c.course_name FROM students s JOIN courses c ON s.course_id = c.id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Manage Students</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        .content {
            padding: 30px; /* Match padding from manage-rooms.php */
        }
        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }
        .search-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .search-container input {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 200px;
        }
        .table-container {
            overflow-x: auto; /* Allow horizontal scrolling on small screens */
        }
        table {
            width: 100%;
            min-width: 1000px; /* Ensure table has enough width to display content */
            border-collapse: collapse;
            background-color: #f9f9f9; /* Subtle background */
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        th, td {
            padding: 12px 16px; /* Balanced padding for better spacing */
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2; /* Match header style */
            font-weight: bold;
        }
        /* Define specific widths for each column */
        th:nth-child(1), td:nth-child(1) { /* Name column */
            width: 25%; /* Increased width for longer names */
        }
        th:nth-child(2), td:nth-child(2) { /* RoomNo column */
            width: 15%; /* Enough space for dropdown */
        }
        th:nth-child(3), td:nth-child(3) { /* Course column */
            width: 25%; /* Increased width for longer course names */
        }
        th:nth-child(4), td:nth-child(4) { /* Year of Study column */
            width: 15%; /* Enough space for dropdown */
        }
        th:nth-child(5), td:nth-child(5) { /* Actions column */
            width: 20%; /* Enough space for buttons */
        }
        input[type="text"],
        select {
            width: 100%; /* Full width for consistency */
            padding: 8px; /* Match input padding */
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
            display: block; /* Prevent wrapping */
        }
        .btn-sm {
            padding: 5px 10px; /* Match button size */
        }
        .btn-success {
            background-color: #5cb85c; /* Green button style */
        }
        .btn-success:hover {
            background-color: #4cae4c; /* Darker green on hover */
        }
        .btn-danger {
            background-color: #d9534f; /* Red button style */
        }
        .btn-danger:hover {
            background-color: #c9302c; /* Darker red on hover */
        }
        .btn-info {
            background-color: #5bc0de; /* Blue for Read More */
        }
        .btn-info:hover {
            background-color: #31b0d5; /* Darker blue on hover */
        }
        .action-buttons {
            display: flex;
            gap: 10px; /* Space between buttons */
            align-items: center;
            flex-wrap: nowrap; /* Prevent buttons from wrapping */
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
        .student-details {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .student-details div {
            display: flex;
            justify-content: space-between;
            padding: 5px 0;
            border-bottom: 1px solid #eee;
        }
        .student-details label {
            font-weight: bold;
            color: #333;
            width: 40%;
        }
        .student-details span {
            color: #555;
            width: 60%;
            text-align: right;
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
    </div>
    <div class="content">
        <div class="header-container">
            <h2>Manage Students</h2>
            <div class="search-container">
                <input type="text" id="searchInput" placeholder="Search by Name or RoomNo">
            </div>
        </div>
        <div class="table-container">
            <table id="studentsTable">
                <tr>
                    <th>Name</th>
                    <th>RoomNo</th>
                    <th>Course</th>
                    <th>Year of Study</th>
                    <th>Actions</th>
                </tr>
                <?php while ($row = $result->fetch_assoc()) { ?>
                <tr class="student-row">
                    <td>
                        <form method="POST" action="" style="display:inline;" class="student-form" data-id="<?php echo $row['id']; ?>">
                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                            <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                    </td>
                    <td>
                            <select name="room" required>
                                <?php
                                $rooms_result->data_seek(0); // Reset pointer for the dropdown
                                while ($room = $rooms_result->fetch_assoc()) { ?>
                                    <option value="<?php echo $room['room_number']; ?>" <?php if ($room['room_number'] == $row['room']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($room['room_number']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                    </td>
                    <td>
                            <select name="course_id" required>
                                <?php
                                $courses_result->data_seek(0); // Reset pointer for the dropdown
                                while ($course = $courses_result->fetch_assoc()) { ?>
                                    <option value="<?php echo $course['id']; ?>" <?php if ($course['id'] == $row['course_id']) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($course['course_name']); ?>
                                    </option>
                                <?php } ?>
                            </select>
                    </td>
                    <td>
                            <select name="year_of_study" required>
                                <option value="1" <?php if ($row['year_of_study'] == 1) echo 'selected'; ?>>1st Year</option>
                                <option value="2" <?php if ($row['year_of_study'] == 2) echo 'selected'; ?>>2nd Year</option>
                                <option value="3" <?php if ($row['year_of_study'] == 3) echo 'selected'; ?>>3rd Year</option>
                                <option value="4" <?php if ($row['year_of_study'] == 4) echo 'selected'; ?>>4th Year</option>
                            </select>
                    </td>
                    <td>
                            <div class="action-buttons">
                                <!-- Hidden fields for editing -->
                                <input type="hidden" name="email" value="<?php echo htmlspecialchars($row['email']); ?>">
                                <input type="hidden" name="contact_number" value="<?php echo htmlspecialchars($row['contact_number']); ?>">
                                <input type="hidden" name="emergency_contact" value="<?php echo htmlspecialchars($row['emergency_contact']); ?>">
                                <input type="hidden" name="gender" value="<?php echo htmlspecialchars($row['gender']); ?>">
                                <input type="hidden" name="date_of_birth" value="<?php echo $row['date_of_birth']; ?>">
                                <input type="hidden" name="registration_date" value="<?php echo $row['registration_date']; ?>">
                                <input type="hidden" name="address" value="<?php echo htmlspecialchars($row['address']); ?>">
                                <input type="hidden" name="guardian_name" value="<?php echo htmlspecialchars($row['guardian_name']); ?>">
                                <input type="hidden" name="guardian_contact" value="<?php echo htmlspecialchars($row['guardian_contact']); ?>">
                                <button type="submit" name="update" class="btn btn-success btn-sm">Update</button>
                            </form>
                            <a href="?delete=<?php echo $row['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this student?');">Delete</a>
                            <button type="button" class="btn btn-info btn-sm" data-toggle="modal" data-target="#detailsModal<?php echo $row['id']; ?>">Read More</button>
                        </div>
                    </td>
                </tr>
                <!-- Modal for student details -->
                <div class="modal fade" id="detailsModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="detailsModalLabel<?php echo $row['id']; ?>">
                    <div class="modal-dialog" role="document">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                                <h4 class="modal-title" id="detailsModalLabel<?php echo $row['id']; ?>">Student Details</h4>
                            </div>
                            <div class="modal-body">
                                <div class="student-details">
                                    <div>
                                        <label>ID:</label>
                                        <span><?php echo $row['id']; ?></span>
                                    </div>
                                    <div>
                                        <label>Name:</label>
                                        <span><?php echo htmlspecialchars($row['name']); ?></span>
                                    </div>
                                    <div>
                                        <label>Email:</label>
                                        <span><?php echo htmlspecialchars($row['email']); ?></span>
                                    </div>
                                    <div>
                                        <label>Contact Number:</label>
                                        <span><?php echo htmlspecialchars($row['contact_number']); ?></span>
                                    </div>
                                    <div>
                                        <label>Emergency Contact:</label>
                                        <span><?php echo htmlspecialchars($row['emergency_contact']); ?></span>
                                    </div>
                                    <div>
                                        <label>Gender:</label>
                                        <span><?php echo htmlspecialchars($row['gender']); ?></span>
                                    </div>
                                    <div>
                                        <label>Date of Birth:</label>
                                        <span><?php echo $row['date_of_birth']; ?></span>
                                    </div>
                                    <div>
                                        <label>Course:</label>
                                        <span><?php echo htmlspecialchars($row['course_name']); ?></span>
                                    </div>
                                    <div>
                                        <label>Year of Study:</label>
                                        <span><?php echo $row['year_of_study']; ?>th Year</span>
                                    </div>
                                    <div>
                                        <label>Room:</label>
                                        <span><?php echo htmlspecialchars($row['room']); ?></span>
                                    </div>
                                    <div>
                                        <label>Registration Date:</label>
                                        <span><?php echo $row['registration_date']; ?></span>
                                    </div>
                                    <div>
                                        <label>Address:</label>
                                        <span><?php echo htmlspecialchars($row['address']); ?></span>
                                    </div>
                                    <div>
                                        <label>Guardian Name:</label>
                                        <span><?php echo htmlspecialchars($row['guardian_name']); ?></span>
                                    </div>
                                    <div>
                                        <label>Guardian Contact:</label>
                                        <span><?php echo htmlspecialchars($row['guardian_contact']); ?></span>
                                    </div>
                                </div>
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
    <script>
        $(document).ready(function() {
            // Form validation for date of birth
            $('.student-form').on('submit', function(e) {
                const dob = new Date($(this).find('input[name="date_of_birth"]').val());
                const today = new Date('2025-05-13');
                const age = today.getFullYear() - dob.getFullYear();
                const monthDiff = today.getMonth() - dob.getMonth();
                if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
                    age--;
                }
                if (age < 16) {
                    alert('Student must be at least 16 years old.');
                    e.preventDefault();
                }
            });

            // Search functionality
            $('#searchInput').on('keyup', function() {
                const searchText = $(this).val().toLowerCase();
                $('#studentsTable .student-row').each(function() {
                    const name = $(this).find('td:eq(0) input[name="name"]').val().toLowerCase();
                    const roomNo = $(this).find('td:eq(1) select[name="room"]').val().toLowerCase();
                    if (name.includes(searchText) || roomNo.includes(searchText)) {
                        $(this).show();
                    } else {
                        $(this).hide();
                    }
                });
            });
        });
    </script>
</body>
</html>
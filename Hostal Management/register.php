<?php
session_start();
include('includes/dbconn.php');

// Fetch courses for dropdown
$courses_result = $mysqli->query("SELECT id, course_name FROM courses ORDER BY course_name ASC");

// Fetch rooms for dropdown
$rooms_result = $mysqli->query("SELECT room_number FROM rooms ORDER BY room_number ASC");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and retrieve form data
    $name = $mysqli->real_escape_string($_POST['name']);
    $email = $mysqli->real_escape_string($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password
    $contact_number = $mysqli->real_escape_string($_POST['contact_number']);
    $emergency_contact = $mysqli->real_escape_string($_POST['emergency_contact']);
    $gender = $_POST['gender'];
    $date_of_birth = $_POST['date_of_birth'];
    $course_id = (int)$_POST['course_id'];
    $year_of_study = (int)$_POST['year_of_study'];
    $room = $mysqli->real_escape_string($_POST['room']);
    $address = $mysqli->real_escape_string($_POST['address']);
    $guardian_name = $mysqli->real_escape_string($_POST['guardian_name']);
    $guardian_contact = $mysqli->real_escape_string($_POST['guardian_contact']);
    $registration_date = date('Y-m-d'); // Today's date: 2025-05-13

    // Check if email already exists
    $check_email = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
    $check_email->bind_param('s', $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        echo "<script>alert('Email already registered. Please use a different email.');</script>";
    } else {
        // Insert into students table
        $stmt = $mysqli->prepare("INSERT INTO students (name, email, contact_number, emergency_contact, gender, date_of_birth, course_id, year_of_study, room, registration_date, address, guardian_name, guardian_contact) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('ssssssiiissss', $name, $email, $contact_number, $emergency_contact, $gender, $date_of_birth, $course_id, $year_of_study, $room, $registration_date, $address, $guardian_name, $guardian_contact);

        if ($stmt->execute()) {
            // Get the inserted student ID
            $student_id = $stmt->insert_id;

            // Insert into users table for login credentials
            $user_stmt = $mysqli->prepare("INSERT INTO users (student_id, email, password) VALUES (?, ?, ?)");
            $user_stmt->bind_param('iss', $student_id, $email, $password);
            
            if ($user_stmt->execute()) {
                echo "<script>alert('Registration successful! Please log in.'); window.location.href='userlogin.php';</script>";
            } else {
                echo "<script>alert('Error creating user account: " . addslashes($user_stmt->error) . "');</script>";
                // Delete the student record if user creation fails to maintain consistency
                $mysqli->query("DELETE FROM students WHERE id = $student_id");
            }
            $user_stmt->close();
        } else {
            echo "<script>alert('Error registering student: " . addslashes($stmt->error) . "');</script>";
        }
        $stmt->close();
    }
    $check_email->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>User Registration - Hostel Management System</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .registration-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 30px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .registration-container h2 {
            text-align: center;
            margin-bottom: 30px;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
            color: #555;
        }
        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        .form-group textarea {
            resize: vertical;
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
        }
        .btn-submit:hover {
            background-color: #4cae4c;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
        }
        .login-link a {
            color: #337ab7;
            text-decoration: none;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="registration-container">
        <h2>User Registration</h2>
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="name">Full Name *</label>
                        <input type="text" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" id="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password *</label>
                        <input type="password" id="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label for="contact_number">Contact Number *</label>
                        <input type="text" id="contact_number" name="contact_number" required>
                    </div>
                    <div class="form-group">
                        <label for="emergency_contact">Emergency Contact *</label>
                        <input type="text" id="emergency_contact" name="emergency_contact" required>
                    </div>
                    <div class="form-group">
                        <label for="gender">Gender *</label>
                        <select id="gender" name="gender" required>
                            <option value="">Select Gender</option>
                            <option value="Male">Male</option>
                            <option value="Female">Female</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_of_birth">Date of Birth *</label>
                        <input type="date" id="date_of_birth" name="date_of_birth" required>
                    </div>
                    <div class="form-group">
                        <label for="course_id">Course *</label>
                        <select id="course_id" name="course_id" required>
                            <option value="">Select Course</option>
                            <?php while ($course = $courses_result->fetch_assoc()) { ?>
                                <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="year_of_study">Year of Study *</label>
                        <input type="number" id="year_of_study" name="year_of_study" min="1" max="5" required>
                    </div>
                    <div class="form-group">
                        <label for="room">Room Number *</label>
                        <select id="room" name="room" required>
                            <option value="">Select Room</option>
                            <?php while ($room = $rooms_result->fetch_assoc()) { ?>
                                <option value="<?php echo htmlspecialchars($room['room_number']); ?>"><?php echo htmlspecialchars($room['room_number']); ?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea id="address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="guardian_name">Guardian Name *</label>
                        <input type="text" id="guardian_name" name="guardian_name" required>
                    </div>
                    <div class="form-group">
                        <label for="guardian_contact">Guardian Contact *</label>
                        <input type="text" id="guardian_contact" name="guardian_contact" required>
                    </div>
                </div>
            </div>
            <button type="submit" class="btn-submit">Register</button>
        </form>
        <div class="login-link">
            <p>Already have an account? <a href="userlogin.php">Log in here</a></p>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</body>
</html>
<?php
// Free result sets
$courses_result->free();
$rooms_result->free();
?>
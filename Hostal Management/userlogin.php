<?php
session_start();
include('includes/dbconn.php');

if (isset($_POST['login'])) {
    $email = $mysqli->real_escape_string($_POST['username']); // Using 'username' field as email
    $password = $_POST['password'];

    // Fetch user from the users table
    $stmt = $mysqli->prepare("SELECT id, student_id, email, password FROM users WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Login successful
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['student_id'] = $user['student_id'];
        $_SESSION['user_email'] = $user['email'];
        echo "<script>alert('Login successful! Welcome, " . htmlspecialchars($email) . "'); window.location.href='user/dashboard.php';</script>";
    } else {
        echo "<script>alert('Invalid Email or Password');</script>";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
            background-color: #f5f5f5;
        }
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            padding-top: 20px;
            position: fixed;
            height: 100%;
        }
        .sidebar h2 {
            color: white;
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar a {
            padding: 15px 20px;
            display: block;
            color: white;
            text-decoration: none;
            font-size: 16px;
        }
        .sidebar a:hover {
            background-color: #34495e;
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
        form {
            max-width: 400px;
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #555;
        }
        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #5cb85c;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background-color: #4cae4c;
        }
        .forgot-password {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #337ab7;
            text-decoration: none;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Hostel Management System</h2>
        <a href="index.php">Main</a>
        <a href="register.php">User Registration</a>
        <a href="userlogin.php">User Login</a>
        <a href="admin/adminpage.php">Admin Login</a>
    </div>
    <div class="content">
        <h2>User Login</h2>
        <form method="POST" action="">
            <label>Email ID</label>
            <input type="text" name="username" placeholder="Please fill in this field" required>
            <label>Password</label>
            <input type="password" name="password" required>
            <button type="submit" name="login">Login</button>
            <a href="#" class="forgot-password">Forgot password?</a>
        </form>
    </div>
</body>
</html>